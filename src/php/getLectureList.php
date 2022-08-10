<?php
    include($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/lib/php-webdriver/vendor/autoload.php");
    include($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/lib/simplehtmldom.php");
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Facebook\WebDriver\Chrome\ChromeOptions;
    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\WebDriverExpectedCondition;
    use Facebook\WebDriver\WebDriverBy;
    use Facebook\WebDriver\Chrome\ChromeDriver;
    use Facebook\WebDriver\Chrome\ChromeProfile;

    if (!isset($_SESSION)) {
        session_start();
    }

    if (isset($_SESSION["nn_username"]) && isset($_SESSION["nn_userkey"])) {
        if (isset($_SESSION["courseDatas"])) {
            echo json_encode(array("result"=>"success", "message"=>$_SESSION["courseDatas"]));
            return;
        }

        $userName = $_SESSION["nn_username"];
        $userPassword = $_SESSION["nn_userkey"];
        $serverUrl = 'selenium server ip';
        $desiredCapabilities = DesiredCapabilities::chrome();
    
        $desiredCapabilities->setCapability('acceptSslCerts', false);
        $desiredCapabilities->setCapability('pageLoadStrategy', 'eager');
        
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(['--headless', '--disable-gpu', '--disable-extentions', '--window-size=1920,1080', '--proxy-server="direct://"', '--proxy-bypass-list=*', '--start-maximized']);
        $desiredCapabilities->setCapability(ChromeOptions::CAPABILITY, $chromeOptions);
        
        $driver = RemoteWebDriver::create($serverUrl, $desiredCapabilities);
    
        $driver->get("https://kulms.korea.ac.kr");
    
        $driver->wait()->until(WebDriverExpectedCondition::titleIs("고려대학교-원활한 강의 진행을 위한 블랙보드 안내 페이지"));
        $driver->executeScript("newLocation();");
        $driver->wait()->until(WebDriverExpectedCondition::titleIs("Korea University SAML Login"));
    
        $driver->findElement(WebDriverBy::id("one_id"))->sendKeys($userName);
        $driver->findElement(WebDriverBy::id("password"))->sendKeys($userPassword);
        $driver->executeScript("login.userTypeCheck();");
        $driver->wait()->until(WebDriverExpectedCondition::titleIs("활동 스트림"));
    
        $driver->executeScript("location.href = 'https://kulms.korea.ac.kr/ultra/messages';");
        $driver->wait()->until(WebDriverExpectedCondition::titleIs("메시지"));
        sleep(10);
        $courseDatas = [];
    
        $result = $driver->findElement(WebDriverBy::id("container-term-current"))->getAttribute("innerHTML");
        $html = str_get_html($result);
        $messages = $html->find("//div[@ng-repeat='membership in baseMessages.getCoursesByTerm(term.id)']");
        foreach ($messages as $message) {
            $dataCourseName = $message->find("//a[@ng-if='!courseConversations.isDisableTab']", 0)->innertext;
            $dataCourseId = $message->{"data-course-id"};
    
            $courseDatas[$dataCourseName] = $dataCourseId;
        }
        $driver->quit();
        $_SESSION["courseDatas"] = $courseDatas;
        echo json_encode(array("result"=>"success", "message"=>$courseDatas));
    }
    else {
        echo json_encode(array("result"=>"failed", "message"=>"Invalid parameter"));
    }
?>
