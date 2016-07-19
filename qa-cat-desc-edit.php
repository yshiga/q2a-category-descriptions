<?php

class qa_cat_descriptions_edit_page {

	function match_request($request)
	{
		$parts=explode('/', $request);

		return $parts[0] == 'cat-edit';
	}

	function process_request($request)
	{
		$parts = explode('/', $request);
		$categoryslugs = $parts[1];
		$categoryid = qa_db_select_with_pending(qa_db_slugs_to_category_id_selectspec($categoryslugs));
		$category = qa_db_select_with_pending(qa_db_full_category_selectspec($categoryid, true));

		$qa_content = qa_content_prepare();
		$qa_content['title']=qa_lang_html_sub('plugin_cat_desc/edit_desc_for_x', qa_html($category['title']));

		if (qa_user_permit_error('plugin_cat_desc_permit_edit')) {
			$qa_content['error']=qa_lang_html('users/no_permission');
			return $qa_content;
		}

		require_once QA_INCLUDE_DIR.'qa-db-metas.php';

		if (qa_clicked('dosave')) {
			require_once QA_INCLUDE_DIR.'qa-util-string.php';

			// $taglc = qa_strtolower($tag);
			qa_db_categorymeta_set($categoryid, 'title', qa_post_text('cattitle'));
			qa_db_categorymeta_set($categoryid, 'description', qa_post_text('catdesc'));
			qa_db_categorymeta_set($categoryid, 'icon', qa_post_text('caticon'));

			qa_redirect($categoryslugs);
		}

		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',

			'style' => 'tall', // could be 'wide'


			'fields' => array(
				array(
					'label' => 'Title:',
					'type' => 'text',
					'rows' => 2,
					'tags' => 'NAME="cattitle" ID="cattitle"',
					'value' => qa_html(qa_db_categorymeta_get($categoryid, 'title')),
				),
				array(
					'label' => 'Description:',
					'type' => 'text',
					'rows' => 4,
					'tags' => 'NAME="catdesc" ID="catdesc"',
					'value' => qa_html(qa_db_categorymeta_get($categoryid, 'description')),
				),
				array(
					'label' => 'Icon image:',
					'type' => 'text',
					'rows' => 1,
					'tags' => 'NAME="caticon" ID="caticon"',
					'value' => qa_html(qa_db_categorymeta_get($categoryid, 'icon')),
				),
			),
			'buttons' => array(
				array(
					'tags' => 'NAME="dosave"',
					'label' => qa_lang_html('plugin_cat_desc/save_desc_button'),
				),
			),
		);

		$qa_content['focusid']='catdesc';
		return $qa_content;
	}

}
