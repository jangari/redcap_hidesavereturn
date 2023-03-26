<?php namespace INTERSECT\HideSaveReturn;

use \REDCap as REDCap;

class HideSaveReturn extends \ExternalModules\AbstractExternalModule {

protected static $Tags = array(
    '@HIDESAVERETURN' => array('description'=>'HIDESAVERETURN Action Tags<br/>Hides the Save and Return Later button on surveys <em>if the field is visible due to branching logic</em>.'), 
);

protected function makeTagTR($tag, $description) {
                global $isAjax, $lang;
                return \RCView::tr(array(),
			\RCView::td(array('class'=>'nowrap', 'style'=>'text-align:center;background-color:#f5f5f5;color:#912B2B;padding:7px 15px 7px 12px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-right:0;'),
				((!$isAjax || (isset($_POST['hideBtns']) && $_POST['hideBtns'] == '1')) ? '' :
					\RCView::button(array('class'=>'btn btn-xs btn-rcred', 'style'=>'', 'onclick'=>"$('#field_annotation').val(trim('".js_escape($tag)." '+$('#field_annotation').val())); highlightTableRowOb($(this).parentsUntil('tr').parent(),2500);"), $lang['design_171'])
				)
			) .
			\RCView::td(array('class'=>'nowrap', 'style'=>'background-color:#f5f5f5;color:#912B2B;padding:7px;font-weight:bold;border:1px solid #ccc;border-bottom:0;border-left:0;border-right:0;'),
				$tag
			) .
			\RCView::td(array('style'=>'line-height:1.3;font-size:13px;background-color:#f5f5f5;padding:7px;border:1px solid #ccc;border-bottom:0;border-left:0;'),
				'<i class="fas fa-cube mr-1"></i>'.$description
			)
		);

}

public function redcap_every_page_before_render($project_id) {
    if (PAGE==='Design/action_tag_explain.php') {
        global $lang;
        $lastActionTagDesc = end(\Form::getActionTags());

        // which $lang element is this?
        $langElement = array_search($lastActionTagDesc, $lang);

        foreach (static::$Tags as $tag => $tagAttr) {
            $lastActionTagDesc .= "</td></tr>";
            $lastActionTagDesc .= $this->makeTagTR($tag, $tagAttr['description']);
        }
        $lang[$langElement] = rtrim(rtrim(rtrim(trim($lastActionTagDesc), '</tr>')),'</td>');
    }
}


    function getTags($tag) {
        // This is straight out of Andy Martin's example post on this:
        // https://community.projectredcap.org/questions/32001/custom-action-tags-or-module-parameters.html
        if (!class_exists('INTERSECT\HideSaveReturn\ActionTagHelper')) include_once('classes/ActionTagHelper.php');
        $action_tag_results = ActionTagHelper::getActionTags($tag);
        return $action_tag_results;
    }

    function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance) {

        // Get array of fields in current instruments
        $currInstrumentFields = REDCap::getFieldNames($instrument);

        // Define the action tag
        $hideSaveReturnTag = "@HIDESAVERETURN";

        // construct an array of fields annotated with the tag
        $hideSaveReturnFields = array();

        $fields = $this->getTags($hideSaveReturnTag);
        if (empty($fields)) return;
        $fields = array_keys($fields[$hideSaveReturnTag]);
        $hideSaveReturnFields = array_merge((array)$hideSaveReturnFields,(array)$fields); 

        // Return the intersection of those two arrays
        $hideSaveReturnFields = array_values(array_intersect((array)$hideSaveReturnFields, (array)$currInstrumentFields));

        // Create a JS array to feed into our JS script
        echo "<script type=\"text/javascript\">const hideSaveReturnFields = [];";
        for ($i = 0; $i < count($hideSaveReturnFields); $i++){
            // Push each field to the JS array
            echo "hideSaveReturnFields.push('". $hideSaveReturnFields[$i] ."');";
        }
        echo "$(document).ready(function(){
            $(function(){
                function hideSaveReturnButton(hideSaveReturnFields) {
                    hideSaveReturn = 0;
                    hideSaveReturnFields.forEach(function(field) {
                        if ($('#' + field + '-tr').is(':visible')) {
                            hideSaveReturn += 1;
                        };
                    });
                        if (hideSaveReturn > 0) {
                            $('button[name=\"submit-btn-savereturnlater\"]').parent().parent().hide();
                        } else {
                            $('button[name=\"submit-btn-savereturnlater\"]').parent().parent().show();
                        };
                    };
                hideSaveReturnButton(hideSaveReturnFields);
                const callback = function(mutation, observer) {
                    hideSaveReturnButton(hideSaveReturnFields);
                };
                const observer = new MutationObserver(callback);
                targetFields = hideSaveReturnFields
                targetFields.forEach(function(field) {
                    const node = document.getElementById(field+'-tr');
                    if (node){
                        observer.observe(node, {attributes: true});
                    }
                });
            });
        });
        </script>";
    }
}
