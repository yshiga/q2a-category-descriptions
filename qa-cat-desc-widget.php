<?php

class qa_cat_descriptions_widget {

	function allow_template($template)
	{
		return ($template === 'qa' || $template === 'questions');
	}

	function allow_region($region)
	{
		return true;
	}

	function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
	{
		require_once QA_INCLUDE_DIR.'qa-db-metas.php';

		if ($template === 'questions') {
			$parts=explode('/', $request);
			if (isset($parts[1])) {
				$categoryslugs = $parts[1];
			} else {
				$categoryslugs = '';
			}
			
		} elseif ($template === 'qa') {
			$categoryslugs = $request;
		}
		// $categoryid = cat_desc_db::get_category_id($categoryslugs);
		// $categoryid = qa_db_select_with_pending(qa_db_slugs_to_category_id_selectspec($categoryslugs));
		$category = qa_db_select_with_pending(qa_db_full_category_selectspec($categoryslugs, false));

		if (empty($categoryslugs) || empty($category)) return;

		$start = qa_get_start();
		if ($start <= 0) {
			$html = $this->get_category_description($category);
			$themeobject->output($html);
		}
	}

	function option_default($option)
	{
		if ($option == 'plugin_cat_desc_max_len')
			return 250;

		if ($option == 'plugin_cat_desc_font_size')
			return 18;
		if ($option == 'plugin_cat_desc_sidebar_html')
			return 1;
		// if ($option == 'plugin_cat_desc_enable_icon')
		// 	return 1;
		// if ($option == 'plugin_cat_desc_icon_height')
		// 	return 18;
		// if ($option == 'plugin_cat_desc_icon_width')
		// 	return 18;
		if ($option == 'plugin_cat_desc_permit_edit') {
			require_once QA_INCLUDE_DIR.'qa-app-options.php';
			return QA_PERMIT_EXPERTS;
		}

		return null;
	}

	function admin_form(&$qa_content)
	{
		require_once QA_INCLUDE_DIR.'qa-app-admin.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';

		$permitoptions = qa_admin_permit_options(QA_PERMIT_USERS, QA_PERMIT_SUPERS, false, false);

		$saved = false;

		if (qa_clicked('plugin_cat_desc_save_button')) {
			qa_opt('plugin_cat_desc_max_len', (int)qa_post_text('plugin_cat_desc_ml_field'));
			qa_opt('plugin_cat_desc_font_size', (int)qa_post_text('plugin_cat_desc_fs_field'));
			qa_opt('plugin_cat_desc_permit_edit', (int)qa_post_text('plugin_cat_desc_pe_field'));
			// qa_opt('plugin_cat_desc_enable_icon', (int)qa_post_text('plugin_cat_desc_enable_icon_field'));
			// qa_opt('plugin_cat_desc_icon_height', (int)qa_post_text('plugin_cat_desc_icon_height_field'));
			// qa_opt('plugin_cat_desc_icon_width', (int)qa_post_text('plugin_cat_desc_icon_width_field'));
			$saved=true;
		}
			// qa_set_display_rules($qa_content, array(
			// 	'plugin_cat_desc_icon_height' => 'plugin_cat_desc_enable_icon_field',
			// 	'plugin_cat_desc_icon_width' => 'plugin_cat_desc_enable_icon_field',
			// ));
		return array(
			'ok' => $saved ? qa_lang_html('plugin_cat_desc/ok_message') : null,

			'fields' => array(
				array(
					'label' => qa_lang_html('plugin_cat_desc/maxlength'),
					'type' => 'number',
					'value' => (int)qa_opt('plugin_cat_desc_max_len'),
					'suffix' => qa_lang_html('plugin_cat_desc/characters'),
					'tags' => 'NAME="plugin_cat_desc_ml_field"',
				),
				// array(
				// 	'label' => 'Enable Images in Category links',
				// 	'type' => 'checkbox',
				// 	'value' => qa_opt('plugin_cat_desc_enable_icon'),
				// 	'tags' => 'NAME="plugin_cat_desc_enable_icon_field" ID="plugin_cat_desc_enable_icon_field"',
				// ),
				// array(
				// 	'id' => 'plugin_cat_desc_icon_height',
				// 	'label' => 'image height:',
				// 	'suffix' => 'pixels',
				// 	'type' => 'number',
				// 	'value' => (int)qa_opt('plugin_cat_desc_icon_height'),
				// 	'tags' => 'NAME="plugin_cat_desc_icon_height_field"',
				// ),
				// array(
				// 	'id' => 'plugin_cat_desc_icon_width',
				// 	'label' => 'image width :',
				// 	'suffix' => 'pixels',
				// 	'type' => 'number',
				// 	'value' => (int)qa_opt('plugin_cat_desc_icon_width'),
				// 	'tags' => 'NAME="plugin_cat_desc_icon_width_field"',
				// ),
				array(
					'label' => qa_lang_html('plugin_cat_desc/enable_html'),
					'type' => 'checkbox',
					'value' => (int)qa_opt('plugin_cat_desc_sidebar_html'),
					'tags' => 'NAME="plugin_cat_desc_sidebar_html_field"',
				),
				array(
					'label' => qa_lang_html('plugin_cat_desc/starting_font_size'),
					'type' => 'number',
					'value' => (int)qa_opt('plugin_cat_desc_font_size'),
					'suffix' => qa_lang_html('plugin_cat_desc/pixels'),
					'tags' => 'NAME="plugin_cat_desc_fs_field"',
				),

				array(
					'label' => qa_lang_html('plugin_cat_desc/allow_editing'),
					'type' => 'select',
					'value' => @$permitoptions[qa_opt('plugin_cat_desc_permit_edit')],
					'options' => $permitoptions,
					'tags' => 'NAME="plugin_cat_desc_pe_field"',
				),
			),

			'buttons' => array(
				array(
					'label' => qa_lang_html('plugin_cat_desc/save_changes'),
					'tags' => 'NAME="plugin_cat_desc_save_button"',
				),
			),
		);
	}

	private function get_category_description($category)
	{
		$html = '';
		$description = $category['content'];
		if (!(qa_opt('plugin_cat_desc_sidebar_html'))) {
			$description = qa_html($description);
		}

		$editurlhtml = qa_path_html('admin/categories').'?edit='.$category['categoryid'];

		$allowediting = !qa_user_permit_error('plugin_cat_desc_permit_edit');

		if ($allowediting) {
			$editing = ' - <A HREF="'.$editurlhtml.'">'.qa_lang_html('plugin_cat_desc/edit').'</A>';
		} else {
			$editing = '';
		}

		if (strlen($description)) {
			$path = QA_PLUGIN_DIR.'q2a-category-descriptions/html/description_template.html';
			$template = file_get_contents($path);
			$params = $this->get_params($category, $description, $editing);
			return strtr($template, $params);
		} elseif ($allowediting) {
			return '<A HREF="'.$editurlhtml.'">'.qa_lang_html('plugin_cat_desc/create_desc_link').'</A>';
		}

	}

	private function get_params($category, $description, $editing)
	{
		$categoryid = $category['categoryid'];
		$title=qa_db_categorymeta_get($categoryid, 'category_title');
		$headline=qa_db_categorymeta_get($categoryid, 'category_headline');
		$desc2=qa_db_categorymeta_get($categoryid, 'category_desc');
		$imageurl=qa_db_categorymeta_get($categoryid, 'category_background_image');
		$dates = $this->get_recent_category_date($categoryid);
		$recent_date = @$dates['prefix'].@$dates['data'].@$dates['suffix'];

		return array(
			'^imageurl' => $imageurl,
			'^category' => $category['title'],
			'^title' => $title,
			'^description' => $description,
			'^headline' => $headline,
			'^desc2' => $desc2,
			'^editing' => $editing,
			'^recent_title' => qa_lang_html('plugin_cat_desc/recent_title'),
			'^recent_date' => $recent_date,
		);
	}

	private function get_recent_category_date($categoryid)
	{
		$sql = "SELECT UNIX_TIMESTAMP(created) as created";
		$sql.= " FROM ^posts";
		$sql.= " WHERE type = 'Q'";
		$sql.= " AND categoryid = #";
		$sql.= " ORDER BY created DESC";
		$sql.= " LIMIT 1";

		$created = qa_db_read_one_value(qa_db_query_sub($sql, $categoryid));

		if(!empty($created)) {
			$fulldatedays = qa_opt('show_full_date_days');
			$dates = qa_when_to_html($created, $fulldatedays);
			return $dates;
		} else {
			return array(
				'prefix' => '',
				'data' => '',
				'suffix' => '',
			);
		}
	}

}
