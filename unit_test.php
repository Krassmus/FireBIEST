<?php

# Copyright (c)  2007-2011 - Marcus Lunzenauer <mlunzena@uos.de>, Rasmus Fuhse <fuhse@data-quest.de>
#
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.


# set error reporting
error_reporting(E_ALL & ~E_NOTICE);

# set include path
$inc_path = ini_get('include_path');
$inc_path .= PATH_SEPARATOR . dirname(__FILE__) . '/../../../..';
$inc_path .= PATH_SEPARATOR . dirname(__FILE__) . '/../../../../config';
ini_set('include_path', $inc_path);

# load required files
require_once dirname(__file__).'/classes/simpletest/unit_tester.php';
require_once dirname(__file__).'/classes/simpletest/reporter.php';
require_once dirname(__file__).'/classes/simpletest/collector.php';

# load varstream for easier filesystem testing
require_once dirname(__file__).'/classes/varstream.php';

# load DB Mock
if ($_REQUEST['db']) {
    include dirname(__file__)."/loadDBMock.php";
}
$GLOBALS['testing'] = true;

# collect all tests
$all = new TestSuite('All tests');
if ($_REQUEST['db']) {
    $collector = new SimplePatternCollector('/test[A-Z|a-z]{0,2}\.php$/');
} else {
    $collector = new SimplePatternCollector('/test\.php$/');
}
if ($_REQUEST['path']) {
    $folder = dirname(__file__)."/../../../plugins_packages/".$_REQUEST['path'];
    if (file_exists($folder . '/lib')) {
        $all->collect($folder . '/lib', $collector);
    }
    if (file_exists($folder . '/tests')) {
        $all->collect($folder . '/tests', $collector);
    }
    if (file_exists($folder . '/lib/classes')) {
        $all->collect($folder . '/lib/classes', $collector);
    }
}

$all->run(new TextReporter());


//Abräumen der mock_db_ Tabellen:
if ($_REQUEST['clean']) {
    DBManager::get()->dropMockTables();
}