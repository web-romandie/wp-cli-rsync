<?php

use Mustangostang\Spyc;

if (!class_exists('WP_CLI')) {
    return;
}

/**
 * Rsync files
 *
 * @when after_wp_load
 */
WP_CLI::add_command(
    'rsync',
    function ($args, $assoc_args) {
        try {
            $env = WP_CLI\Utils\get_flag_value($assoc_args, 'env');

            if (empty($env)) {
                WP_CLI::error('Please provide an environment. Like --env=staging or --env=production.');
            }

            $env = '@' . $env;

            $aliases = WP_CLI::get_configurator()->get_aliases();

            if (!isset($aliases[$env])) {
                WP_CLI::error("Environment $env doesn't seem present in your WP-CLI config.");
            }

            if (!isset($aliases[$env]['ssh']) && !isset($aliases[$env]['path'])) {
                WP_CLI::error("The $env environment doesn't have a 'ssh' of 'path' setting in your WP-CLI config.");
            }

            // do some url parsing
            $sshUrl = rtrim($aliases[$env]['ssh'], '/');
            $sshUrlParts = parse_url($sshUrl);
            $uploads = wp_upload_dir();
            $uploadUrlParts = parse_url($uploads['baseurl']);

            // Get the chance to find a document root config
            $localYamlFile = 'wp-cli.yml';
            $config = is_file($localYamlFile)
                ? Spyc::YAMLLoad($localYamlFile)
                : [];

            $cmd = sprintf(
                'rsync -avz  -e "ssh -p %s" %s@%s:%s/%s%s %s%s',
                empty($sshUrlParts['port']) ? 21 : $sshUrlParts['port'],
                $sshUrlParts['user'],
                $sshUrlParts['host'],
                $sshUrlParts['path'],
                isset($config['server']['docroot']) ? $config['server']['docroot'] . '/' : '',
                ltrim($uploadUrlParts['path'], '/'),
                isset($config['server']['docroot']) ? $config['server']['docroot'] . '/' : '',
                dirname(ltrim($uploadUrlParts['path'], '/'))
            );

            // Transfer all uploaded files
            WP_CLI::log('- Transfering uploads folder.');
            passthru($cmd);

            WP_CLI::success("Sync complete.");
        } catch (Exception $error) {
            WP_CLI::error($error->getMessage());
        }
    }
);