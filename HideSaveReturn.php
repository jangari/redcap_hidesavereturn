<?php namespace INTERSECT\HideSaveReturn;

use \REDCap as REDCap;

class HideSaveReturn extends \ExternalModules\AbstractExternalModule {

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
