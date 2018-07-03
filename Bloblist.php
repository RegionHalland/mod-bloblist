<?php 

namespace Modularity\Module\Accordion;

use GuzzleHttp\Client;

class Bloblist extends \Modularity\Module
{
    public $slug = 'mod-bloblist';
    public $nameSingular = 'Bloblist';
    public $namePlural = 'Bloblists';
    public $description = 'List files from azure blob storage by tag';
    public $templateDir = BLOBLIST_MODULE_PATH . 'templates';

    private $azureApiKey = 'NO_ESPOSIBLE';

    public function init()
    {
        $this->nameSingular = __('Bloblist', 'modularity');
        $this->namePlural =   __('Bloblists', 'modularity');
        $this->description =  __('Outputs list of files from azure blob storage', 'modularity');

        $this->getOptions();

        add_filter('acf/load_field/key=field_5b3b3e7d874b0', array($this, 'populateAcfFields'));
    }

    public function template()
    {
        return 'modularity-mod-bloblist.blade.php';
    }

    public function script()
    {
        // Not working :(
        // https://github.com/helsingborg-stad/Modularity/issues/34
        wp_register_script('mod-bloblist-js', plugins_url( '/assets/js/mod-bloblist.js', __FILE__ ), array(), '', true);
        wp_enqueue_script('mod-bloblist-js');
    }

    /**
     * Returns data to view
     * https://github.com/helsingborg-stad/Modularity/blob/7d435e3610d5cb25d984e6aaaeb3960b9c2ada56/modularity-custom-module-example/ImageModule.php#L23
     * @return mixed
     */
    public function data() : array
    {
        return get_fields('tags');
    }


    /**
     * Get facets from Azure search service
     * @return object
     */
    private function getFacets()
    {
        // Don't store this here
        $url = 'NO_ESPOSIBLE';
        $options = array(
            'headers' => [
                'Content-Type' => 'application/json',
                'api-key' => $this->azureApiKey
            ]
        );
        
        $client = new \GuzzleHttp\Client();

        $result = $client->request('GET', $url, $options);

        return $result;
    }

    /**
     * Populate the ACF options field with tags
     * https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
     * @return object
     */
    public function populateAcfFields($field)
    {
        $result = $this->getFacets();
        $result = json_decode($result->getBody()->getContents());
        $tags = $result->{'@search.facets'}->tags;

        if (empty($tags)) {
            return false;
        }

        $field['choices'] = array();

        foreach ($tags as $tag) {
            $value = $tag->value;
            $field['choices'][$value] = $value;
        }

        return $field;
    }

    public function getOptions()
    {
        var_dump();
    }

}
