<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit_Selenium
 * @author     Giorgio Sironi <giorgio.sironi@asp-poli.it>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 1.2.0
 */

/**
 * Browser session for Selenium 2: main point of entry for functionality.
 *
 * @package    PHPUnit_Selenium
 * @author     Giorgio Sironi <giorgio.sironi@asp-poli.it>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @version    Release: @package_version@
 * @link       http://www.phpunit.de/
 * @since      Class available since Release 1.2.0
 * @method void acceptAlert() Press OK on an alert, or confirms a dialog
 * @method mixed alertText($value = NULL) Gets the alert dialog text, or sets the text for a prompt dialog
 * @method void back()
 * @method void dismissAlert() Press Cancel on an alert, or does not confirm a dialog
 * @method string execute(array $javaScriptCode) Injects arbitrary JavaScript in the page and returns the last. See unit tests for usage
 * @method string executeAsync(array $javaScriptCode) Injects arbitrary JavaScript and wait for the callback (last element of arguments) to be called. See unit tests for usage
 * @method void forward()
 * @method void frame($elementId) Changes the focus to a frame in the page
 * @method void refresh()
 * @method string source() Returns the HTML source of the page
 * @method string title()
 * @method void|string url($url = NULL)
 * @method void window($name) Changes the focus to another window
 * @method string windowHandle() Retrieves the current window handle
 * @method string windowHandles() Retrieves a list of all available window handles
 * @method string keys() Send a sequence of key strokes to the active element.
 */
class PHPUnit_Extensions_Selenium2TestCase_Session
    extends PHPUnit_Extensions_Selenium2TestCase_CommandsHolder
{
    /**
     * @var string  the base URL for this session,
     *              which all relative URLs will refer to
     */
    private $baseUrl;

    /**
     * @var PHPUnit_Extensions_Selenium2TestCase_Session_Timeouts
     */
    private $timeouts;

    /**
     * @var boolean
     */
    private $stopped = FALSE;

    public function __construct($driver,
                                PHPUnit_Extensions_Selenium2TestCase_URL $url,
                                PHPUnit_Extensions_Selenium2TestCase_URL $baseUrl,
                                PHPUnit_Extensions_Selenium2TestCase_Session_Timeouts $timeouts)
    {
        $this->baseUrl = $baseUrl;
        $this->timeouts = $timeouts;
        parent::__construct($driver, $url);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->url->lastSegment();
    }

    protected function initCommands()
    {
        $baseUrl = $this->baseUrl;
        return array(
            'acceptAlert' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_AcceptAlert',
            'alertText' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_AlertText',
            'back' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'buttondown' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'buttonup' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'dismissAlert' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_DismissAlert',
            'execute' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'executeAsync' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'forward' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'frame' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Frame',
            'keys' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Keys',
            'moveto' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_MoveTo',
            'refresh' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'screenshot' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericAccessor',
            'source' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_GenericAccessor',
            'title' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_GenericAccessor',
            'url' => function ($jsonParameters, $commandUrl) use ($baseUrl) {
                return new PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Url($jsonParameters, $commandUrl, $baseUrl);
            },
            'window' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Window',
            'windowHandle' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_GenericAccessor',
            'windowHandles' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_GenericAccessor',
            'touchDown' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'touchUp' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'touchMove' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'touchScroll' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'flick' => 'PHPUnit_Extensions_Selenium2TestCase_ElementCommand_GenericPost',
            'location' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Location',
            'orientation' => 'PHPUnit_Extensions_Selenium2TestCase_SessionCommand_Orientation'
        );
    }


    protected function initCommandsMap()
    {
        $this->commandsMap = array(
            'touchDown' => 'touch/down',
            'touchUp' => 'touch/up',
            'touchMove' => 'touch/move',
            'touchScroll' => 'touch/scroll',
            'flick' => 'touch/flick'
        );
    }

    /**
     * @params string $commandClass     a class name, descending from
                                        PHPUnit_Extensions_Selenium2TestCase_Command
     * @return callable
     */
    private function factoryMethod($commandClass)
    {
        return function($jsonParameters, $url) use ($commandClass) {
            return new $commandClass($jsonParameters, $url);
        };
    }

    public function __destruct()
    {
        $this->stop();
    }

    /**
     * @return PHPUnit_Extensions_Selenium2TestCase_URL
     */
    public function getSessionUrl()
    {
        return $this->url;
    }

    /**
     * Closed the browser.
     * @return void
     */
    public function stop()
    {
        if ($this->stopped) {
            return;
        }
        try {
            $this->driver->curl('DELETE', $this->url);
        } catch (Exception $e) {
        }
        $this->stopped = TRUE;
    }

    /**
     * @param string $value     e.g. 'container'
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function byClassName($value)
    {
        return $this->by('class name', $value);
    }

    /**
     * @param string $value     e.g. 'div.container'
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function byCssSelector($value)
    {
        return $this->by('css selector', $value);
    }

    /**
     * @param string $value     e.g. 'uniqueId'
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function byId($value)
    {
        return $this->by('id', $value);
    }

    /**
     * @param string $value     e.g. 'email_address'
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function byName($value)
    {
        return $this->by('name', $value);
    }

    /**
     * @param string $value     e.g. '/div[@attribute="value"]'
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function byXPath($value)
    {
        return $this->by('xpath', $value);
    }

    /**
     * @param string $value     e.g. 'Link text'
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function byLinkText($value)
    {
        return $this->by('link text', $value);
    }

    /**
     * @param string $strategy     supported by JsonWireProtocol element/ command
     * @param string $value
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    protected function by($strategy, $value)
    {
        return $this->element($this->using($strategy)->value($value));
    }

    /**
     * @param string $strategy
     * @return PHPUnit_Extensions_Selenium2TestCase_ElementCriteria
     */
    public function using($strategy)
    {
        return new PHPUnit_Extensions_Selenium2TestCase_ElementCriteria($strategy);
    }

    /**
     * @return PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function element(PHPUnit_Extensions_Selenium2TestCase_ElementCriteria $criteria)
    {
        $value = $this->postCommand('element', $criteria);
        return PHPUnit_Extensions_Selenium2TestCase_Element::fromResponseValue($value,
                                                                               $this->url->descend('element'),
                                                                               $this->driver);
    }

    /**
     * @return array    instances of PHPUnit_Extensions_Selenium2TestCase_Element
     */
    public function elements(PHPUnit_Extensions_Selenium2TestCase_ElementCriteria $criteria)
    {
        $values = $this->postCommand('elements', $criteria);
        $elements = array();
        foreach ($values as $value) {
            $elements[] = PHPUnit_Extensions_Selenium2TestCase_Element::fromResponseValue($value, $this->url->descend('element'), $this->driver);
        }
        return $elements;
    }

    /**
     * @return PHPUnit_Extensions_Selenium2TestCase_Element_Select
     */
    public function select(PHPUnit_Extensions_Selenium2TestCase_Element $element)
    {
        $tag = $element->name();
        if ($tag !== 'select') {
            throw new InvalidArgumentException("The element is not a `select` tag but a `$tag`.");
        }
        return PHPUnit_Extensions_Selenium2TestCase_Element_Select::fromElement($element);
    }

    /**
     * @param string $id    id attribute, e.g. 'container'
     * @return void
     */
    public function clickOnElement($id)
    {
        return $this->element($this->using('id')->value($id))->click();
    }

    public function timeouts()
    {
        return $this->timeouts;
    }

    /**
     * @return string   a BLOB of a PNG file
     */
    public function currentScreenshot()
    {
        return base64_decode($this->screenshot());
    }

    /**
     * @return PHPUnit_Extensions_Selenium2TestCase_Window
     */
    public function currentWindow()
    {
        $url = $this->url->descend('window')->descend($this->windowHandle());
        return new PHPUnit_Extensions_Selenium2TestCase_Window($this->driver, $url);
    }

    public function closeWindow()
    {
        $this->driver->curl('DELETE', $this->url->descend('window'));
    }

    /**
     * @return PHPUnit_Extensions_Selenium2TestCase_Session_Cookie
     */
    public function cookie()
    {
        $url = $this->url->descend('cookie');
        return new PHPUnit_Extensions_Selenium2TestCase_Session_Cookie($this->driver, $url);
    }

    /**
     * @return PHPUnit_Extensions_Selenium2TestCase_Session_Storage
     */
    public function localStorage()
    {
        $url = $this->url->addCommand('localStorage');
        return new PHPUnit_Extensions_Selenium2TestCase_Session_Storage($this->driver, $url);
    }

    public function landscape()
    {
        $this->orientation('LANDSCAPE');
    }

    public function portrait()
    {
        $this->orientation('PORTRAIT');
    }
}
