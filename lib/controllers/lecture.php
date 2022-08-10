<?php
    use Facebook\WebDriver\Remote\RemoteWebDriver;
    use Facebook\WebDriver\Chrome\ChromeOptions;
    use Facebook\WebDriver\Remote\DesiredCapabilities;
    use Facebook\WebDriver\WebDriverExpectedCondition;
    use Facebook\WebDriver\WebDriverBy;
    use Facebook\WebDriver\Chrome\ChromeDriver;
    use Facebook\WebDriver\Chrome\ChromeProfile;

    class LectureController extends Controller
    {    
        protected $sql;

        function __construct()
        {
            global $sql;
            $this->sql = $sql;
        }

        function ListAction()
        {
            if (!$this->IsSigned()) {
                $this->Output(false);
            }

            if (isset($_SESSION["coursedata"]) && $_SESSION["coursedata"] != "") {
                $this->Output($_SESSION["coursedata"]);
            }

            $userName = $_SESSION["username"];
            $userPassword = AESDecrypt($_SESSION["bbpassword"], $_SESSION["privatekey"]);
            $serverUrl = 'http://123.215.206.28:4444';
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
            sleep(3);        
            $result = $driver->findElement(WebDriverBy::id("container-term-current"))->getAttribute("innerHTML");
            $driver->quit();

            $courseDatas = [];
            $html = str_get_html($result);
            $messages = $html->find("//div[@ng-repeat='membership in baseMessages.getCoursesByTerm(term.id)']");
            foreach ($messages as $message) {
                $dataCourseName = $message->find("//a[@ng-if='!courseConversations.isDisableTab']", 0)->innertext;
                $dataCourseId = $message->{"data-course-id"};
        
                $courseDatas[$dataCourseName] = $dataCourseId;
            }
            $_SESSION["coursedata"] = $courseDatas;
            $this->Output($courseDatas);
        }

        function StatusAction()
        {
            if (!$this->IsSigned()) {
                $this->Output(false);
            }

            $lectureId = $_POST["lectureid"];
            if ($lectureId == "") {
                $this->Output(false);
            }

            $userName = $_SESSION["username"];
            $userPassword = AESDecrypt($_SESSION["bbpassword"], $_SESSION["privatekey"]);
            $serverUrl = 'http://123.215.206.28:4444';
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
                $driver->quit();
                
                if ($listHtml["error"] != "script timeout" && $listHtml != null) {
                    $this->Output($listHtml);
                }
                else {
                    $this->Output(false);
                }
            }
            catch (Exception $ex) {
                $this->Output(false);
            }
        }
    }
?>