<?php

add_action( 'after_setup_theme', 'uncc_add_background_support' );



function uncc_add_background_support()
{
	add_theme_support( 'custom-background' );
}

