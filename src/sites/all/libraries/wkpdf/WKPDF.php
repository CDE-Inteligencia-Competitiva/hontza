<?php

/**
 * This class was produced by PHP-4-Business.co.uk and is based on the classes from
 * aur1mas <aur1mas@devnet.lt>  --  https://github.com/aur1mas/Wkhtmltopdf
 *      and
 * uuf6429@gmail.com / contact@covac-software.com  --  http://code.google.com/p/wkhtmltopdf/wiki/IntegrationWithPhp
 *
 * Authorship and copyright of those classes is unclear - they both claim authorship although the code is largely identical!
 * From:
 *              https://github.com/aur1mas/Wkhtmltopdf
 *              @author Aurimas Baubkus aka aur1mas <aur1mas@devnet.lt>
 *              @license Released under "New BSD license"
 * and
 *              http://code.google.com/p/wkhtmltopdf/wiki/IntegrationWithPhp
 *              @copyright 2010 Christian Sciberras / Covac Software.
 *              @license None. There are no restrictions on use, however keep copyright intact.
 *                      Modification is allowed, keep track of modifications below in this comment block.
 *
 *
 *
 * Manual for wkhtmltopdf 0.10.0 rc2
 *      http://madalgo.au.dk/~jakobt/wkhtmltoxdoc/wkhtmltopdf_0.10.0_rc2-doc.html
 *
 * Raw settings
 *      http://www.cs.au.dk/~jakobt/libwkhtmltox_0.10.0_doc/pagesettings.html
 *
 *
 * This class allows to use either the binary program ( http://code.google.com/p/wkhtmltopdf/ )
 * or the PHP extension ( https://github.com/mreiferson/php-wkhtmltox )
 *
 * Note: to use many of the useful parameters (e.g. headers and footers) you need a patched version of QT.
 * This is included within the statically compiled binary, but if you compile the binary yourself or compile the
 * PHP extension then you must patch QT yourself before compilation (see http://code.google.com/p/wkhtmltopdf/wiki/compilation)
 *
 *
 * Written and tested on Centos 5.4 + PHP 5.2.12
 *
 *
 * Sample Usage
 * ------------
 * 
 *     try {
 *         $wkhtmltopdf = new wkhtmltopdf(array('path' => APPLICATION_PATH . '/../public/uploads/'));
 *         $wkhtmltopdf->setTitle("Title");
 *         $wkhtmltopdf->setHtml("Content");
 *         $wkhtmltopdf->output(wkhtmltopdf::MODE_DOWNLOAD, "file.pdf");
 *     } catch (Exception $e) {
 *         echo $e->getMessage();
 *     }
 * 
 *
 *
 * Alternatively
 * -------------
 *      $headerhtml & $footerhtml will be repeated on each page
 *
 * 
 *     $headerhtml = '<html><head></head><body><table width="100%" border="0"><tr><td width="100%"><img src="' . $_SERVER['HTTP_HOST'] . '/logo.png" /><span style="float:right;font-size:12px">Some Text</span></td></tr></table></body></html>';
 *
 *     $footerhtml = '<html><head></head><body><table width="100%" border="0"><tr><td width="100%" style="text-align:center;font-size:10px;color:blue;">1 Anystreet, Anytown, Anycounty&nbsp;&nbsp;&nbsp;tel: 01234 567890&nbsp;&nbsp;&nbsp;mail@address.co.uk</td></tr></table></body></html>';
 *
 *     $wkhtmloptions .= '--header-spacing 5 --footer-spacing 2 --grayscale --margin-top 15';
 *
 *     $pdf = new wkhtmltopdf(array( 'title'         => 'Title',
 *                                   'html'          => 'Content',
 *                                   'tmppath'       => $_SERVER['DOCUMENT_ROOT'].'tmp',
 *                                   'binpath'       => $_SERVER['DOCUMENT_ROOT'].'bin/',
 *                                   'header_html'   => $headerhtml,
 *                                   'footer_html'   => $footerhtml,
 *                                   'options'       => $wkhtmloptions,
 *                                  ) );
 *     $pdf->output('I', 'document.pdf');
 * 
 *
 *
 *
 * PHP extension
 * -------------
 * To use the PHP extension instantiate the object with the 'php' param.
 * Note: when using the PHP extension you must use the appropriate 'raw' parameters (e.g. 'header.center') and NOT
 * the binary ones (e.g. '--header-center').
 *    also make sure you put the param in the correct array - some settings are "global" and some are "object" (see the manual)
 *
 *     $wkhtmloptions['global'] = array( 'colorMode' => 'grayscale', 'margin.top' => '15mm' );
 *     $wkhtmloptions['object'] = array( 'header.spacing' => '5mm', 'footer.spacing' => '2mm' );
 *
 *     $pdf = new wkhtmltopdf(array( 'title'         => 'Title',
 *                                   'html'          => 'Content',
 *                                   'tmppath'       => $_SERVER['DOCUMENT_ROOT'].'tmp',
 *                                   'header_html'   => $headerhtml,
 *                                   'footer_html'   => $footerhtml,
 *                                   'options'       => $wkhtmloptions,
 *                                 ) ,'php');
 *
 *     $pdf->output('I', 'document.pdf');
 *
 *
 *
 *
 * When using output() the mode param takes one of 4 values:
 *
 * 'D'  (const MODE_DOWNLOAD = 'D')  - Force the client to download PDF file
 * 'S'  (const MODE_STRING = 'S')    - Returns the PDF file as a string
 * 'I'  (const MODE_EMBEDDED = 'I')  - When possible, force the client to embed PDF file
 * 'F'  (const MODE_SAVE = 'F')      - PDF file is saved on the server. The path+filename is returned.
 *
 * But note that the user's browser settings may override what you ask for!
 *
 *
 */

/**
 * @version 1.01
 */
class wkhtmltopdf {

  /**
   * Setters / getters properties
   */
  protected $_method = null;
  protected $_html = null;
  protected $_httpurl = null;
  protected $_orientation = null;
  protected $_pageSize = null;
  protected $_toc = false;
  protected $_copies = 1;
  protected $_grayscale = false;
  protected $_title = null;
  protected $_headerHtml;
  protected $_footerHtml;
  protected $_httpusername;
  protected $_httppassword;
  protected $_options;

  /**
   * What type of input are we processing?  (url / disc file / html string)
   *
   * @var unknown_type
   */
  protected $_have_httpurl = false;
  protected $_have_htmlfile = false;
  protected $_have_html = false;

  /**
   * Location of wkhtmltopdf executable
   */
  //protected $_binpath = '/usr/local/wkhtmltox/bin/';
  protected $_binpath = '/usr/local/bin/';
  //protected $_binname = 'wkhtmltopdf';
  //intelsat-2015
  protected $_binname = 'wkhtmltopdf.sh';

  /**
   * Location of HTML file
   */
  protected $_htmlfilepath = '/tmp/';
  protected $_htmlfilename = null;
  protected $_tmphtmlfilename = null;

  /**
   * Directory to use for temporary files
   */
  protected $_tmpfilepath = '/tmp/';

  /**
   * Temporary files holding header / footer HTML
   */
  protected $_have_headerhtml = false;
  protected $_have_footerhtml = false;
  protected $_headerfilename = null;
  protected $_footerfilename = null;

  /**
   * Available page orientations
   */

  const ORIENTATION_PORTRAIT = 'Portrait';    // vertical
  const ORIENTATION_LANDSCAPE = 'Landscape';  // horizontal

  /**
   * Page sizes
   */
  const SIZE_A4 = 'A4';
  const SIZE_LETTER = 'letter';

  /**
   * PDF get modes
   */
  const MODE_DOWNLOAD = 'D';                                                                  // Force the client to download PDF file
  const MODE_STRING = 'S';                                                                            // Returns the PDF file as a string
  const MODE_EMBEDDED = 'I';                                                                  // When possible, force the client to embed PDF file
  const MODE_SAVE = 'F';                                                                                      // PDF file is saved on the server. The path+filename is returned.

  /**
   * Constructor: initialize command line and reserve temporary file.
   * @param array $options
   * @param string $method    method to call wkhtmltopdf: 'exec'=binary executable, 'php'=php extension
   * @return bool FALSE on failure
   */

  public function __construct(array $options = array(), $method = 'exec') {        
    switch ($method) {
      case 'exec':
        if (array_key_exists('binpath', $options)) {
          $this->setBinPath($options['binpath']);
        }

        if (array_key_exists('binfile', $options)) {
          $this->setBinFile($options['binfile']);
        }

        /* Check the binary executable exists */
        $this->getBin();
        break;

      case 'php':
        break;

      default:
        throw new Exception('WKPDF unknown method "' . htmlspecialchars($method, ENT_QUOTES) . '"');
        return false;
    }

    $this->setMethod($method);

    // Common to both 'exec' and 'php' method

    if (array_key_exists('html', $options)) {
      $this->setHtml($options['html']);
    }

    if (array_key_exists('orientation', $options)) {
      $this->setOrientation($options['orientation']);
    } else {
      $this->setOrientation(self::ORIENTATION_PORTRAIT);
    }

    if (array_key_exists('page_size', $options)) {
      $this->setPageSize($options['page_size']);
    } else {
      $this->setPageSize(self::SIZE_A4);
    }

    if (array_key_exists('toc', $options)) {
      $this->setTOC($options['toc']);
    }

    if (array_key_exists('grayscale', $options)) {
      $this->setGrayscale($options['grayscale']);
    }

    if (array_key_exists('title', $options)) {
      $this->setTitle($options['title']);
    }

    if (array_key_exists('header_html', $options)) {
      $this->setHeaderHtml($options['header_html']);
    }

    if (array_key_exists('footer_html', $options)) {
      $this->setFooterHtml($options['footer_html']);
    }

    if (array_key_exists('tmppath', $options)) {
      $this->setTmpPath($options['tmppath']);
    }

    if (array_key_exists('options', $options)) {
      $this->setOptions($options['options']);
    }
  }

  /**
   * Attempts to return the library's full help info
   *
   * @return string
   */
  public function getHelp() {
    $r = $this->_exec($this->getBin() . " --extended-help");
    return $r['stdout'];
  }

  /**
   * Set method to call wkhtmltopdf
   *
   * @return null
   */
  public function setMethod($method) {
    $this->_method = $method;
    return;
  }

  /**
   * Get method to call wkhtmltopdf
   *
   * @return string
   */
  public function getMethod() {
    return $this->_method;
  }

  /**
   * Set path to binary executable directory
   *
   * @throws Exception
   * @return null
   */
  public function setBinPath($path) {
    if (realpath($path) === false)
      throw new Exception('Path must be absolute ("' . htmlspecialchars($path, ENT_QUOTES) . '")');

    $this->_binpath = realpath((string) $path) . DIRECTORY_SEPARATOR;
    return;
  }

  /**
   * Get path to binary executable
   *
   * @return string
   */
  public function getBinPath() {
    return $this->_binpath;
  }

  /**
   * Set filename of binary executable
   *
   * @return null
   */
  public function setBinFile($name) {
    $this->_binname = (string) $name;
    return;
  }

  /**
   * Get filename of binary executable
   *
   * @return string
   */
  public function getBinFile() {
    return $this->_binname;
  }

  /**
   * Get the binary executable
   *
   * @throws Exception
   * @return string
   */
  public function getBin() {
    $bin = $this->getBinPath() . $this->getBinFile();
    //intelsat-2015
    //intelsat-2016
    if(defined('_IS_WKPDF_SHOW_EXCEPTION_GET_BIN') && _IS_WKPDF_SHOW_EXCEPTION_GET_BIN!=1){
        return $bin;
    }    
    if (realpath($bin) === false)
        throw new Exception('Path must be absolute ("' . htmlspecialchars($bin, ENT_QUOTES) . '")');
    if (file_exists($bin) === false)
    throw new Exception('WKPDF static executable "' . htmlspecialchars($bin, ENT_QUOTES) . '" was not found');    
    return $bin;
  }

  /**
   * Set absolute path where to store temporary HTML files
   *
   * @throws Exception
   * @param string $path
   * @return null
   */
  public function setTmpPath($path) {
    if (realpath($path) === false)
      throw new Exception('Path must be absolute ("' . htmlspecialchars($path, ENT_QUOTES) . '")');

    $this->_tmpfilepath = realpath($path) . DIRECTORY_SEPARATOR;
    return;
  }

  /**
   * Get path where to store temporary HTML files
   *
   * @return string
   */
  public function getTmpPath() {
    return $this->_tmpfilepath;
  }

  /**
   * Set absolute path where to read HTML file
   *
   * @throws Exception
   * @param string $path
   * @return null
   */
  public function setHtmlPath($path) {
    if (realpath($path) === false)
      throw new Exception('Path must be absolute ("' . htmlspecialchars($path, ENT_QUOTES) . '")');

    $this->_htmlfilepath = realpath($path) . DIRECTORY_SEPARATOR;
    return;
  }

  /**
   * Get path where to read HTML file
   *
   * @return string
   */
  public function getHtmlPath() {
    return $this->_htmlfilepath;
  }

  /**
   * Set filename holding HTML
   *
   * @return null
   */
  public function setHtmlFile($name) {
    $this->_htmlfilename = (string) $name;
    $this->_have_htmlfile = true;
    return;
  }

  /**
   * Get filename holding HTML
   *
   * @return string
   */
  public function getHtmlFile() {
    return $this->_htmlfilename;
  }

  /**
   * Get the path+filename holding HTML
   *
   * @throws Exception
   * @return string
   */
  public function getHtmlPathFile() {
    $file = $this->getHtmlPath() . $this->getHtmlFile();

    if (realpath($file) === false)
      throw new Exception('Path must be absolute ("' . htmlspecialchars($file, ENT_QUOTES) . '")');
    if (file_exists($file) === false)
      throw new Exception('HTML file "' . htmlspecialchars($file, ENT_QUOTES) . '" was not found');

    return $file;
  }

  /**
   * Set page orientation (default is portrait)
   *
   * @param string $orientation
   * @return null
   */
  public function setOrientation($orientation) {
    $this->_orientation = (string) $orientation;
    return;
  }

  /**
   * Returns page orientation
   *
   * @return string
   */
  public function getOrientation() {
    return $this->_orientation;
  }

  /**
   * Set page/paper size (default is A4)
   * @param string $size
   * @return null
   */
  public function setPageSize($size) {
    $this->_pageSize = (string) $size;
    return;
  }

  /**
   * Returns page size
   *
   * @return int
   */
  public function getPageSize() {
    return $this->_pageSize;
  }

  /**
   * Automatically generate a TOC (table of contents) or not (default is disabled)
   *
   * @param boolean $toc
   * @return Wkhtmltopdf
   */
  public function setTOC($toc = true) {
    $this->_toc = (boolean) $toc;
    return;
  }

  /**
   * Get value of whether automatic Table Of Contents generation is set
   *
   * @return boolean
   */
  public function getTOC() {
    return $this->_toc;
  }

  /**
   * Set the number of copies to make (default is 1)
   *
   * @param int $copies
   * @return null
   */
  public function setCopies($copies) {
    $this->_copies = (int) $copies;
    return;
  }

  /**
   * Get number of copies to make
   *
   * @return int
   */
  public function getCopies() {
    return $this->_copies;
  }

  /**
   * Whether to print in grayscale or not (default is off)
   *
   * @param boolean $mode
   * @return null
   */
  public function setGrayscale($mode) {
    $this->_grayscale = (boolean) $mode;
    return;
  }

  /**
   * Get if page will be printed in grayscale
   *
   * @return boolean
   */
  public function getGrayscale() {
    return $this->_grayscale;
  }

  /**
   * Set PDF title (default is HTML <title> of first document)
   *
   * @param string $title
   * @return null
   */
  public function setTitle($title) {
    $this->_title = (string) $title;
    return;
  }

  /**
   * Get PDF document title
   *
   * @throws Exception
   * @return string
   */
  public function getTitle() {
    return $this->_title;
  }

  /**
   *  Set header html (default is null)
   *
   * @param string $header
   * @return null
   */
  public function setHeaderHtml($header) {
    $this->_headerHtml = (string) $header;
    $this->_have_headerhtml = true;
    return;
  }

  /**
   * Get header html
   *
   * @return string
   */
  public function getHeaderHtml() {
    return $this->_headerHtml;
  }

  /**
   *  Set footer html (default is null)
   *
   * @param string $footer
   * @return null
   */
  public function setFooterHtml($footer) {
    $this->_footerHtml = (string) $footer;
    $this->_have_footerhtml = true;
    return;
  }

  /**
   * Get footer html
   *
   * @return string
   */
  public function getFooterHtml() {
    return $this->_footerHtml;
  }

  /**
   * Set http username
   *
   * @param string $username
   * @return null
   */
  public function setUsername($username) {
    $this->_httpusername = (string) $username;
    return;
  }

  /**
   * Get http username
   *
   * @return string
   */
  public function getUsername() {
    return $this->_httpusername;
  }

  /**
   * Set http password
   *
   * @param string $password
   * @return null
   */
  public function setPassword($password) {
    $this->_httppassword = (string) $password;
    return;
  }

  /**
   * Get http password
   *
   * @return string
   */
  public function getPassword() {
    return $this->_httppassword;
  }

  /**
   *  Set any other WKTMLTOPDF options you need
   *
   * @param string $options
   * @return null
   */
  public function setOptions($options) {
    $this->_options = $options;
    return;
  }

  /**
   * Get any other WKTMLTOPDF options you need
   *
   * @return string
   */
  public function getOptions() {
    return $this->_options;
  }

  /**
   * Set URL to render
   *
   * @param string $html
   * @return null
   */
  public function setHttpUrl($url) {
    $this->_httpurl = (string) $url;
    $this->_have_httpurl = true;
    return;
  }

  /**
   * Get URL to render
   *
   * @return string
   */
  public function getHttpUrl() {
    return $this->_httpurl;
  }

  /**
   * Set HTML content to render (replaces any previous content)
   *
   * @param string $html
   * @return null
   */
  public function setHtml($html) {
    $this->_html = (string) $html;
    $this->_have_html = true;
    return;
  }

  /**
   * Get current HTML content
   *
   * @return string
   */
  public function getHtml() {
    return $this->_html;
  }

  /**
   * Create a temporary file & store the html content
   *
   * @return string   Full path to file
   */
  protected function _createFile($html) {
    $file = $this->_makeFilename();

    file_put_contents($file, $html);
    chmod($file, 0764);

    return $file;
  }

  /**
   * Create a temporary filename
   *
   * @throws Exception
   * @return string
   */
  protected function _makeFilename() {
    if (($path = $this->getTmpPath()) == '') {
      throw new Exception("Path to directory where to store files is not set");
    }
    
    if (realpath($path) === false)
      throw new Exception('Path must be absolute ("' . htmlspecialchars($path, ENT_QUOTES) . '")');

    do {
      $file = mt_rand() . '.html';
    } while (file_exists($path . $file));

    return $path . $file;
  }

  /**
   * Delete a temporary file
   *
   * @param string fn Filename to delete (optional)
   * @return null
   */
  protected function _deleteFile($fn = '') {
    if ($fn !== '') {
      unlink($fn);
    } else {
      // delete our temporary files
      if ($this->_have_html && $this->_tmphtmlfilename) {
        unlink($this->_tmphtmlfilename);
      }
      if ($this->_have_headerhtml && $this->_headerfilename) {
        unlink($this->_headerfilename);
      }
      if ($this->_have_footerhtml && $this->_footerfilename) {
        unlink($this->_footerfilename);
      }
    }

    return;
  }

  /**
   * Returns command to execute
   *
   * @param string    filename of input html
   * @return string
   */
  protected function _getCommand($in) {
    $command = '';

    switch ($this->getMethod()) {
      case 'exec':
        $command = $this->getBin();
        //gemini-2014  
        $wa=9921;  
        $ha=14031;
        $ha=$ha*2;
        //        
        $command .= " --orientation " . $this->getOrientation();
        //$command .= " --page-size " . $this->getPageSize();
        $command .= " --page-width ".$wa."px --page-height ".$ha."px";
        $command .= ($this->getTOC()) ? " --toc" : "";
        $command .= ($this->getGrayscale()) ? " --grayscale" : "";
        $command .= ($this->getTitle()) ? ' --title "' . $this->getTitle() . '"' : "";
        $command .= ($this->getCopies() > 1) ? " --copies " . $this->getCopies() : "";
        $command .= (strlen($this->getPassword()) > 0) ? " --password " . $this->getPassword() . "" : "";
        $command .= (strlen($this->getUsername()) > 0) ? " --username " . $this->getUsername() . "" : "";
        $command .= $this->_have_headerhtml ? " --margin-top 20 --header-html \"" . $this->_headerfilename . "\"" : "";
        $command .= $this->_have_footerhtml ? " --margin-bottom 20 --footer-html \"" . $this->_footerfilename . "\"" : "";
        $command .= ($this->getOptions()) ? " {$this->getOptions()} " : "";

        /*
         * ignore some errors with some urls as recommended with this wkhtmltopdf error message:
         *      Error: Failed loading page <url> (sometimes it will work just to ignore this error with --load-error-handling ignore)
         */
        if ($this->getHttpUrl()) {
          // $command .= ' --load-error-handling ignore';
        }

        $command .= ' "' . $in . '" ';
        $command .= " -";

        break;

      case 'php':
        $command = array();

        $command['global']["orientation"] = $this->getOrientation();
        $command['global']["size.paperSize"] = $this->getPageSize();
        ($this->getTOC()) ? $command['global']["toc"] = 1 : "";
        ($this->getGrayscale()) ? $command['global']["colorMode"] = "Grayscale" : "";
        ($this->getTitle()) ? $command['global']["documentTitle "] = $this->getTitle() : "";
        ($this->getCopies() > 1) ? $command['global']["copies "] = $this->getCopies() : "";
        (strlen($this->getPassword()) > 0) ? $command['object']["load.password"] = $this->getPassword() : "";
        (strlen($this->getUsername()) > 0) ? $command['object']["load.username "] = $this->getUsername() : "";
        if ($this->_have_headerhtml) {
          $command['global']["margin.top"] = "20mm";
          $command['object']["header.htmlUrl"] = 'file://' . $this->_headerfilename;
        }
        if ($this->_have_footerhtml) {
          $command['global']["margin.bottom"] = "20mm";
          $command['object']["footer.htmlUrl"] = 'file://' . $this->_footerfilename;
        }
        $options = $this->getOptions();
        $command['global'] = array_merge($command['global'], $options['global']);
        $command['object'] = array_merge($command['object'], $options['object']);

        break;
    }

    return $command;
  }

  /**
   * Convert HTML to PDF.
   *
   * @todo use file cache
   *
   * @throws Exception
   * @return string
   */
  protected function _render() {
    if ($this->_have_httpurl) {                                                                                                             // source is url
      $input = $this->getHttpUrl();
    } elseif ($this->_have_htmlfile) {                                                                              // source is predefined disc file
      $input = $this->getHtmlPathFile();
    } elseif ($this->_have_html) {                                                                                          // source is html string
      $input = $this->_tmphtmlfilename = $this->_createFile($this->getHtml());
    } else {
      throw new Exception("HTML content or source URL not set");
    }

    if ($this->_have_headerhtml) {
      $this->_headerfilename = $this->_createFile($this->getHeaderHtml());
    }
    if ($this->_have_footerhtml) {
      $this->_footerfilename = $this->_createFile($this->getFooterHtml());
    }

    $command = $this->_getCommand($input);

    // error_log((is_array($command)?print_r($command,true):$command));     // for debug

    switch ($this->getMethod()) {
      case 'exec':

        /* Deprecated - use _pipeExec
          $content = $this->_exec(str_replace('%input%', $input, $this->_getCommand())); */
        //echo print_r($command,1);exit();  
        $content = $this->_pipeExec($command);

        if (strpos(strtolower($content['stderr']), 'error'))
          throw new Exception("System error <pre>" . $content['stderr'] . "</pre>");

        if (strlen($content['stdout']) === 0)
          throw new Exception("WKHTMLTOPDF didn't return any data");

        if ((int) $content['return'] > 1)
          throw new Exception("Shell error, return code: " . (int) $content['return']);

        $data = $content['stdout'];

        break;

      case 'php':
        $command['global']['out'] = $pdffile = $this->_makeFilename();
        $command['object']['page'] = $input;

        // error_log(print_r($command['global'],true)); // for debug
        // error_log(print_r($command['object'],true)); // for debug

        wkhtmltox_convert('pdf', $command['global'], array($command['object']));

        $data = file_get_contents($pdffile);
        $this->_deleteFile($pdffile);

        break;
    }

    return (isset($data) ? $data : false);
  }

  /**
   * Executes the command :  Deprecated - use _pipeExec
   *
   * @param string $cmd   command to execute
   * @param string $input other input (not arguments)
   * @return array
   */
  protected function _exec($cmd, $input = "") {
    $result = array('stdout' => '', 'stderr' => '', 'return' => '');

    $proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    $result['stdout'] = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $result['stderr'] = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $result['return'] = proc_close($proc);

    return $result;
  }

  /**
   * Advanced execution routine.
   *
   * @param string $cmd The command to execute.
   * @param string $input Any input not in arguments.
   * @return array An array of execution data; stdout, stderr and return "error" code.
   */
  private static function _pipeExec($cmd, $input = '') {
    $pipes = array();
    
    $proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, null, null, array('binary_pipes' => true));
    fwrite($pipes[0], $input);
    fclose($pipes[0]);

    // From http://php.net/manual/en/function.proc-open.php#89338
    $read_output = $read_error = false;
    $buffer_len = $prev_buffer_len = 0;
    $ms = 10;
    $stdout = '';
    $read_output = true;
    $stderr = '';
    $read_error = true;
    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);

    // dual reading of STDOUT and STDERR stops one full pipe blocking the other, because the external script is waiting
    while ($read_error != false or $read_output != false) {
      if ($read_output != false) {
        if (feof($pipes[1])) {
          fclose($pipes[1]);
          $read_output = false;
        } else {
          $str = fgets($pipes[1], 1024);
          $len = strlen($str);
          if ($len) {
            $stdout .= $str;
            $buffer_len += $len;
          }
        }
      }

      if ($read_error != false) {
        if (feof($pipes[2])) {
          fclose($pipes[2]);
          $read_error = false;
        } else {
          $str = fgets($pipes[2], 1024);
          $len = strlen($str);
          if ($len) {
            $stderr .= $str;
            $buffer_len += $len;
          }
        }
      }

      if ($buffer_len > $prev_buffer_len) {
        $prev_buffer_len = $buffer_len;
        $ms = 10;
      } else {
        usleep($ms * 1000); // sleep for $ms milliseconds
        if ($ms < 160) {
          $ms = $ms * 2;
        }
      }
    }

    $rtn = proc_close($proc);
    return array(
        'stdout' => $stdout,
        'stderr' => $stderr,
        'return' => $rtn
    );
  }

  /**
   * Return PDF with various options.
   *
   * @param int $mode                 How to output (constants from this same class - c.f. 'PDF get modes')
   * @param string $filename  The PDF's filename (usage depends on $mode)
   */
  public function output($mode, $filename = '') {
    switch ($mode) {
      case self::MODE_DOWNLOAD:
        if (!headers_sent()) {
          $result = $this->_render();
          header("Content-Description: File Transfer");
          header("Cache-Control: public; must-revalidate, max-age=0"); // HTTP/1.1
          header("Pragme: public");
          header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
          header("Last-Modified: " . gmdate('D, d m Y H:i:s') . " GMT");
          header("Content-Type: application/force-download");
          header("Content-Type: application/octet-stream", false);
          header("Content-Type: application/download", false);
          header("Content-Type: application/pdf", false);
          header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
          header("Content-Transfer-Encoding: binary");
          header("Content-Length:" . strlen($result));
          echo $result;
          $this->_deleteFile();
          exit();
        } else {
          throw new Exception("Headers already sent");
        }
        break;
      case self::MODE_STRING:
        return $this->_render();
        break;
      case self::MODE_EMBEDDED:
        if (!headers_sent()) {
          $result = $this->_render();
          header("Content-type: application/pdf");
          header("Cache-control: public, must-revalidate, max-age=0"); // HTTP/1.1
          header("Pragme: public");
          header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
          header("Last-Modified: " . gmdate('D, d m Y H:i:s') . " GMT");
          header("Content-Length: " . strlen($result));
          header('Content-Disposition: inline; filename="' . basename($filename) . '";');
          echo $result;
          $this->_deleteFile();
          exit();
        } else {
          throw new Exception("Headers already sent");
        }
        break;
      case self::MODE_SAVE:
        file_put_contents($filename, $this->_render());
        $this->_deleteFile();
        break;
      default:
        throw new Exception("Mode: " . $mode . " is not supported");
    }
  }

}

?>
