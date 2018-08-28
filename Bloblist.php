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
    private $azureApiKey = '1570F8A4B2C7F2D6739EA20327335175';

    public function init()
    {
        $this->nameSingular = __('Bloblist', 'modularity');
        $this->namePlural =   __('Bloblists', 'modularity');
        $this->description =  __('Outputs list of files from azure blob storage', 'modularity');

        add_filter('acf/load_field/key=field_5b7d3fa982d7e', array($this, 'populateAcfFields'));
    }

    public function template()
    {
        return 'modularity-mod-bloblist.blade.php';
    }

    /**
     * Returns data to the view
     * @return array
     */
    public function data() : array
    {
        $data['lists'] = $this->getBlobList($this->ID);

        return $data;
    }

    /**
     * Gets the checked categories of the provided post ID and returns a list
     * @return array
     */
    public function getBlobList($id)
    { 
        $fields = get_fields($id);

        $lists = [];

        foreach ($fields['collection'] as $collection) {
            
            if (empty($collection['tags'])) {
                return false;
            }

            $list = [
                'title' => $collection['Title'],
                'blobs' => []
            ];

            foreach ($collection['tags'] as $tag) {

                $query = 'api-version=2016-09-01&search=' . $tag;

                $result = $this->fetch( $query, array(
                    'headers' => [ 
                        'Content-Type' => 'application/json', 
                        'api-key' => $this->azureApiKey
                    ])
                );

                $result = json_decode($result->getBody()->getContents());

                $list['count'] = count($result->value);

                foreach($result->value as $path) {
                    $list['blobs'][] = base64_decode($path->metadata_storage_path);
                }

                $lists[] = $list;
            };

           
        }

        return $lists;
    }

    /**
     * Populate the ACF options field with tags
     * https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
     * @return object
     */
    public function populateAcfFields($field)
    {
        $result = $this->fetch( 'api-version=2016-09-01&facet=tags', array(
            'headers' => [ 
                'Content-Type' => 'application/json', 
                'api-key' => $this->azureApiKey
            ])
        );

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

    /**
     * Fetch things from Azure search service
     * @return object
     */
    private function fetch($query, $options)
    {
        $url = 'https://searchcontrolleddocuments.search.windows.net/indexes/azureblob-index/docs?' . $query;
        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET', $url, $options);

        return $result;
    }
}
