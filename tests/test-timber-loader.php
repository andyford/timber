<?php

	class TestTimberLoader extends WP_UnitTestCase {

		function testTwigLoadsFromChildTheme(){
			$this->_setupParentTheme();
			$this->_setupChildTheme();
			$this->assertFileExists(WP_CONTENT_DIR.'/themes/fake-child-theme/style.css');
			switch_theme('fake-child-theme');
			$child_theme = get_stylesheet_directory_uri();
			$this->assertEquals(WP_CONTENT_URL.'/themes/fake-child-theme', $child_theme);
			$context = array();
			$str = Timber::compile('single.twig', $context);
			$this->assertEquals('I am single.twig', trim($str));
		}

		static function _setupChildTheme(){
			$dest_dir = WP_CONTENT_DIR.'/themes/fake-child-theme';
			if (!file_exists($dest_dir)) {
    			mkdir($dest_dir, 0777, true);
			}
			if (!file_exists($dest_dir.'/views')) {
    			mkdir($dest_dir.'/views', 0777, true);
			}
			copy(__DIR__.'/assets/style.css', $dest_dir.'/style.css');
			copy(__DIR__.'/assets/single.twig', $dest_dir.'/views/single.twig');
		}

		function testTwigLoadsFromParentTheme(){
			$this->_setupParentTheme();
			$this->_setupChildTheme();
			switch_theme('fake-child-theme');
			$templates = array('single-parent.twig');
			$str = Timber::compile($templates, array());
			$this->assertEquals('I am single.twig in parent theme', trim($str));
		}

		function _setupParentTheme(){
			$dest_dir = WP_CONTENT_DIR.'/themes/twentythirteen';
			if (!file_exists($dest_dir.'/views')) {
    			mkdir($dest_dir.'/views', 0777, true);
			}
			copy(__DIR__.'/assets/single-parent.twig', $dest_dir.'/views/single.twig');
			copy(__DIR__.'/assets/single-parent.twig', $dest_dir.'/views/single-parent.twig');
		}

		function _setupRelativeViews(){
			if (!file_exists(__DIR__.'/views')) {
    			mkdir(__DIR__.'/views', 0777, true);
			}
			copy(__DIR__.'/assets/relative.twig', __DIR__.'/views/single.twig');
		}

		function _teardownRelativeViews(){
			if (file_exists(__DIR__.'/views/single.twig')){
				unlink(__DIR__.'/views/single.twig');
			}
			if (file_exists(__DIR__.'/views')) {
    			rmdir(__DIR__.'/views');
			}
		}

		function testTwigLoadsFromRelativeToScript(){
			$this->_setupRelativeViews();
			$str = Timber::compile('single.twig');
			$this->assertEquals('I am in the assets directory', trim($str));
			$this->_teardownRelativeViews();
		}

		function testTwigLoadsFromAbsolutePathOnServer(){
			$str = Timber::compile(__DIR__.'/assets/image-test.twig');
			$this->assertEquals('<img src="" />', trim($str));
		}

		function _testTwigLoadsFromAbsolutePathOnServerWithSecurityRestriction(){
			//ini_set('open_basedir', '/srv:/usr:/home/travis/:/tmp:/home:/home/travis/.phpenv/versions/*');
			$str = Timber::compile('assets/single-foo.twig');
			//ini_restore('open_basedir');
		}

		function testTwigLoadsFromAlternateDirName(){
			Timber::$dirname = array('foo', 'views');
			if (!file_exists(get_template_directory().'/foo')) {
    			mkdir(get_template_directory().'/foo', 0777, true);
			}
			copy(__DIR__.'/assets/single-foo.twig', get_template_directory().'/foo/single-foo.twig');
			$str = Timber::compile('single-foo.twig');
			$this->assertEquals('I am single-foo', trim($str));
		}

		function testTwigLoadsFromLocation(){
			Timber::$locations = __DIR__.'/assets';
			$str = Timber::compile('thumb-test.twig');
			$this->assertEquals('<img src="" />', trim($str));
		}


	}
