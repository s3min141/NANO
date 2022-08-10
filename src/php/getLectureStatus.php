<?php
    include($_SERVER["DOCUMENT_ROOT"] . "/NANO/src/php/lib/php-webdriver/vendor/autoload.php");
    if (!isset($_SESSION)) {
        session_start();
    }

    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Facebook\WebDriver\Chrome\ChromeOptions;
    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\WebDriverExpectedCondition;
    use Facebook\WebDriver\WebDriverBy;
    use Facebook\WebDriver\Chrome\ChromeDriver;
    use Facebook\WebDriver\Chrome\ChromeProfile;

    if (isset($_SESSION["nn_username"]) && isset($_SESSION["nn_userkey"]) && isset($_POST["lectureId"])) {
        $lectureId = $_POST["lectureId"];
        $userName = $_SESSION["nn_username"];
        $userPassword = $_SESSION["nn_userkey"];
        $serverUrl = 'selenium server ip';
        try {
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
        
            $driver->executeScript("window.open('https://kulms.korea.ac.kr/webapps/blackboard/execute/blti/launchPlacement?blti_placement_id=136_1&course_id=$lectureId&mode=view', '_self');");
            $driver->wait()->until(WebDriverExpectedCondition::titleIs("사용자별 출결상세보기 – Blackboard Learn"));
            $driver->findElement(WebDriverBy::id("listContainer_showAllButton"))->click();
            $driver->wait()->until(WebDriverExpectedCondition::titleIs("사용자별 출결상세보기 – Blackboard Learn"));
            sleep(5);
            $result = $driver->findElement(WebDriverBy::id("listContainer_datatable"));

            $listHtml = $result->getAttribute("outerHTML");
            if ($listHtml["error"] != "script timeout") {
                echo json_encode(array("result"=>"success", "message"=>$listHtml));
            }
            else {
                echo json_encode(array("result"=>"failed", "message"=>"Video Lecture doesnt exist"));
            }
        }
        catch (Exception $ex) {
            echo json_encode(array("result"=>"failed", "message"=>"Unknown Error, please retry"));
        }
        $driver->quit();
    }
    else {
        echo json_encode(array("result"=>"failed", "message"=>"Invalid parameter"));
    }
?>
