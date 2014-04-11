<?php

return array(
    'caches' => array(
        'filesystem' => array( //can be called directly via SM in the name of 'memcached'
            'adapter' => array(
                'name'     =>'filesystem',
                'lifetime' => 7200,
//                'options'  => array(
//                    'servers'   => array(
//                        array(
//                            '127.0.0.1',11211
//                        )
//                    ),
//                    'namespace'  => 'MYMEMCACHEDNAMESPACE',
//                    'liboptions' => array (
//                        'COMPRESSION' => true,
//                        'binary_protocol' => true,
//                        'no_block' => true,
//                        'connect_timeout' => 100
//                    )
//                )
            ),
            'plugins' => array(
                'exception_handler' => array(
                    'throw_exceptions' => false
                ),
            ),
        ),
    ),
);