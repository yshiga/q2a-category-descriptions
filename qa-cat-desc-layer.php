<?php

class qa_html_theme_layer extends qa_html_theme_base
{
	function head_metas()
	{
		$categoryslug = '';
		if ($this->template === 'questions') {
			$requests = explode('/', $this->request);
			if (isset($requests[1])) {
				$categoryslug = $requests[1];
			}
		} elseif ($this->template === 'qa') {
			$categoryslug = $this->request;
		}

		if (!empty($categoryslug)) {
			require_once QA_INCLUDE_DIR.'qa-db-metas.php';

			$category = qa_db_select_with_pending(qa_db_full_category_selectspec($categoryslug, false));
			$description = $category['content'];
			if (!empty($description)) {
				$this->content['description'] = str_replace(array("\r\n","\n","\r"), " ", strip_tags($description));;
			}
		}

		qa_html_theme_base::head_metas();
	}

}
