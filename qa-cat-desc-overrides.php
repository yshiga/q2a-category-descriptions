<?php
function qa_post_html_fields($post, $userid, $cookieid, $usershtml, $dummy, $options=array())
{
	$fields = qa_post_html_fields_base($post, $userid, $cookieid, $usershtml, $dummy, $options);
	if ($post['basetype'] === 'Q') {
		if (@$options['categoryview'] && isset($post['categoryname']) && isset($post['categorybackpath'])) {
			$favoriteclass='';

			if (count(@$favoritemap['category'])) {
				if (@$favoritemap['category'][$post['categorybackpath']])
					$favoriteclass=' qa-cat-favorited';

				else
					foreach ($favoritemap['category'] as $categorybackpath => $dummy)
						if (substr('/'.$post['categorybackpath'], -strlen($categorybackpath))==$categorybackpath)
							$favoriteclass = ' qa-cat-parent-favorited';
			}
			$category = qa_db_select_with_pending(qa_db_full_category_selectspec($post['categorybackpath'], false));
			$maxlen = (int)qa_opt('plugin_cat_desc_max_len') > 0 ? (int)qa_opt('plugin_cat_desc_max_len') : 250;
			$description =  mb_substr(qa_html($category['content']), 0, $maxlen);
			
			$fields['where'] = qa_lang_html_sub_split('main/in_category_x',
				'<a href="'.qa_path_html(@$options['categorypathprefix'].implode('/', array_reverse(explode('/', $post['categorybackpath'])))).
				'" class="qa-category-link'.$favoriteclass.
				'" original-title="'. $description .'">'.

				qa_html($post['categoryname']).'</a>');
		}
	}
	return $fields;
}

function qa_page_routing()
{
	$routing = qa_page_routing_base();
	$routing['admin/categories'] = CAT_DESC_RELATIVE_PATH . 'pages/admin-categories.php';
	return $routing;
}
