<?php

return array(
    'caches' => array(
        'filesystem' => array(
            'adapter' => array(
                'name'    =>'filesystem', // filesystem, memory, apc are working
                'options' => array('ttl' => 28800), //28800
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            ),
        ),
        'memory' => array( // all stored items will be lost after terminating the script
            'adapter' => array(
                'name'    =>'memory', // filesystem, memory, apc are working
                'options' => array('ttl' => 1800),
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            ),
        ),
    ),
);