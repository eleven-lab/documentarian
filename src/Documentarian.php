<?php

namespace ElevenLab\Documentarian;

use Mni\FrontYAML\Parser;
use Windwalker\Renderer\BladeRenderer;

/**
 * Class Documentarian
 * @package Mpociot\Documentarian
 */
class Documentarian
{

    /**
     * Returns a config value
     *
     * @param string $key
     * @return mixed
     */
    public function config($folder, $key = null)
    {
        $config = include($folder . '/config.php');

        return is_null($key) ? $config : array_get($config, $key);
    }

    /**
     * Create a new API documentation folder and copy all needed files/stubs
     *
     * @param $folder
     */
    public function create($folder)
    {

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
            mkdir($folder . '/public');
            mkdir($folder . '/public/css');
            mkdir($folder . '/public/js');
            mkdir($folder . '/assets');
            mkdir($folder . '/source');
        }

        // copy stub files
        copy(__DIR__ . '/../resources/stubs/gitignore.stub', $folder . '/.gitignore');
        copy(__DIR__ . '/../resources/stubs/package.json', $folder . '/package.json');
        copy(__DIR__ . '/../resources/stubs/gulpfile.js', $folder . '/gulpfile.js');
        copy(__DIR__ . '/../resources/stubs/config.php', $folder . '/config.php');
        copy(__DIR__ . '/../resources/stubs/js/all.js', $folder . '/public/js/all.js');
        copy(__DIR__ . '/../resources/stubs/css/style.css', $folder . '/public/css/style.css');

        // copy raw assets
        rcopy(__DIR__ . '/../resources/stylus', $folder . '/assets/stylus');
        rcopy(__DIR__ . '/../resources/images', $folder . '/assets/images');
        rcopy(__DIR__ . '/../resources/js', $folder . '/assets/js');

        // copy calculated resources
        rcopy(__DIR__ . '/../resources/images/', $folder . '/public/images');
        rcopy(__DIR__ . '/../resources/js/', $folder . '/public/js');
        rcopy(__DIR__ . '/../resources/stylus/fonts', $folder . '/public/css/fonts');
        rcopy(__DIR__ . '/../resources/views/' , $folder . '/views');
    }


    /**
     * Create a new API documentation version folder and copy all needed stubs
     *
     * @param version
     */
    public function createVersion($folder)
    {

        if(!is_dir($folder)){
            mkdir($folder);
            mkdir($folder . '/includes');
        }

        copy(__DIR__ . '/../resources/stubs/index.md', $folder . '/index.md');
        copy(__DIR__ . '/../resources/stubs/includes/_errors.md', $folder . '/includes/_errors.md');


    }

    /**
     * Generate the API documentation using the markdown and include files and provided versions
     *
     * @param $folder
     * @param $apiVersions
     */
    public function generate($folder, $apiVersions)
    {

        if(count($apiVersions) > 0) {
            $_versions = $apiVersions;
        }else{
            $_versions = array_map('basename', array_values(array_filter(glob($folder . '/*'), 'is_dir')));
        }

        foreach($_versions as $version) {
            info('Generating documentation for version ' . $version);
            $res = $this->generateVersion($folder, $version);
            if(!$res){
                output("<fg=red>Could not generate documentation for version '$version': no such file or directory</>");
            }
        }

    }

    /**
     * Generate the API documentation using the markdown and include files
     *
     * @param $folder
     * @return false|null
     */
    public function generateVersion($folder, $version)
    {
        $source_dir = $folder . '/' . $version;

        if (!is_dir($source_dir)) {
            return false;
        }

        $parser = new Parser();

        $document = $parser->parse(file_get_contents($source_dir . '/index.md'));

        $frontmatter = $document->getYAML();
        $html = $document->getContent();

        $renderer = new BladeRenderer([$folder . '/../views'], ['cache_path' => $source_dir . '/_tmp']);

        // Parse and include optional include markdown files
        if (isset($frontmatter['includes'])) {
            foreach ($frontmatter['includes'] as $include) {
                if (file_exists($include_file = $source_dir . '/includes/_' . $include . '.md')) {
                    $document = $parser->parse(file_get_contents($include_file));
                    $html .= $document->getContent();
                }
            }
        }

        // Parse versions and include into toc
        if(!isset($frontmatter['versions'])){
            $apiVersions = [];
        }else {
            $apiVersions = $frontmatter['versions'];
        }
        $apiVersions = !isset($frontmatter['versions']) ? [] : $frontmatter['versions'];
        asort($apiVersions);

        $output = $renderer->render('index', [
            'currentVersion' => $version,
            'versions' => $apiVersions,
            'page' => $frontmatter,
            'content' => $html
        ]);

        file_put_contents($folder . '/../public/' . $version . '.html', $output);

        return true;
    }

    public function getVersions($folder) {

        $_versions = array_map('basename', array_values(array_filter(glob($folder . '/*'), 'is_dir')));

        return $_versions;

    }

}