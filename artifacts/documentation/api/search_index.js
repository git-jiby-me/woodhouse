var search_data = {
    'index': {
        'searchIndex': ["icecave","icecave\\woodhouse","icecave\\woodhouse\\buildstatus","icecave\\woodhouse\\buildstatus\\readers","icecave\\woodhouse\\console","icecave\\woodhouse\\console\\command","icecave\\woodhouse\\console\\command\\github","icecave\\woodhouse\\console\\helper","icecave\\woodhouse\\coverage","icecave\\woodhouse\\coverage\\readers","icecave\\woodhouse\\git","icecave\\woodhouse\\github","icecave\\woodhouse\\publisher","icecave\\woodhouse\\buildstatus\\buildstatus","icecave\\woodhouse\\buildstatus\\readers\\commandlinereader","icecave\\woodhouse\\buildstatus\\readers\\junitreader","icecave\\woodhouse\\buildstatus\\readers\\phpunitjsonreader","icecave\\woodhouse\\buildstatus\\readers\\tapreader","icecave\\woodhouse\\buildstatus\\statusimageselector","icecave\\woodhouse\\buildstatus\\statusreaderfactory","icecave\\woodhouse\\buildstatus\\statusreaderinterface","icecave\\woodhouse\\console\\application","icecave\\woodhouse\\console\\command\\github\\abstractgithubcommand","icecave\\woodhouse\\console\\command\\github\\createauthorizationcommand","icecave\\woodhouse\\console\\command\\github\\deleteauthorizationcommand","icecave\\woodhouse\\console\\command\\github\\listauthorizationscommand","icecave\\woodhouse\\console\\command\\publishcommand","icecave\\woodhouse\\console\\helper\\hiddeninputhelper","icecave\\woodhouse\\coverage\\coverageimageselector","icecave\\woodhouse\\coverage\\coveragereaderfactory","icecave\\woodhouse\\coverage\\coveragereaderinterface","icecave\\woodhouse\\coverage\\readers\\commandlinereader","icecave\\woodhouse\\coverage\\readers\\phpunittextreader","icecave\\woodhouse\\github\\githubclient","icecave\\woodhouse\\github\\githubclientfactory","icecave\\woodhouse\\git\\git","icecave\\woodhouse\\packageinfo","icecave\\woodhouse\\publisher\\abstractpublisher","icecave\\woodhouse\\publisher\\githubpublisher","icecave\\woodhouse\\publisher\\publisherinterface","icecave\\woodhouse\\buildstatus\\readers\\commandlinereader::__construct","icecave\\woodhouse\\buildstatus\\readers\\commandlinereader::readstatus","icecave\\woodhouse\\buildstatus\\readers\\junitreader::__construct","icecave\\woodhouse\\buildstatus\\readers\\junitreader::readstatus","icecave\\woodhouse\\buildstatus\\readers\\phpunitjsonreader::__construct","icecave\\woodhouse\\buildstatus\\readers\\phpunitjsonreader::readstatus","icecave\\woodhouse\\buildstatus\\readers\\tapreader::__construct","icecave\\woodhouse\\buildstatus\\readers\\tapreader::readstatus","icecave\\woodhouse\\buildstatus\\statusimageselector::imagefilename","icecave\\woodhouse\\buildstatus\\statusreaderfactory::__construct","icecave\\woodhouse\\buildstatus\\statusreaderfactory::supportedtypes","icecave\\woodhouse\\buildstatus\\statusreaderfactory::create","icecave\\woodhouse\\buildstatus\\statusreaderinterface::readstatus","icecave\\woodhouse\\console\\application::__construct","icecave\\woodhouse\\console\\application::vendorpath","icecave\\woodhouse\\console\\command\\github\\abstractgithubcommand::__construct","icecave\\woodhouse\\console\\command\\github\\abstractgithubcommand::clientfactory","icecave\\woodhouse\\console\\command\\github\\abstractgithubcommand::setapplication","icecave\\woodhouse\\console\\command\\github\\createauthorizationcommand::__construct","icecave\\woodhouse\\console\\command\\github\\deleteauthorizationcommand::__construct","icecave\\woodhouse\\console\\command\\github\\listauthorizationscommand::__construct","icecave\\woodhouse\\console\\command\\publishcommand::__construct","icecave\\woodhouse\\console\\command\\publishcommand::resolvethemes","icecave\\woodhouse\\console\\command\\publishcommand::enqueueimages","icecave\\woodhouse\\console\\helper\\hiddeninputhelper::__construct","icecave\\woodhouse\\console\\helper\\hiddeninputhelper::getname","icecave\\woodhouse\\console\\helper\\hiddeninputhelper::hiddeninputpath","icecave\\woodhouse\\console\\helper\\hiddeninputhelper::askhiddenresponse","icecave\\woodhouse\\coverage\\coverageimageselector::__construct","icecave\\woodhouse\\coverage\\coverageimageselector::roundpercentage","icecave\\woodhouse\\coverage\\coverageimageselector::imagefilename","icecave\\woodhouse\\coverage\\coverageimageselector::errorimagefilename","icecave\\woodhouse\\coverage\\coverageimageselector::unknownimagefilename","icecave\\woodhouse\\coverage\\coveragereaderfactory::__construct","icecave\\woodhouse\\coverage\\coveragereaderfactory::supportedtypes","icecave\\woodhouse\\coverage\\coveragereaderfactory::create","icecave\\woodhouse\\coverage\\coveragereaderinterface::readpercentage","icecave\\woodhouse\\coverage\\readers\\commandlinereader::__construct","icecave\\woodhouse\\coverage\\readers\\commandlinereader::readpercentage","icecave\\woodhouse\\coverage\\readers\\phpunittextreader::__construct","icecave\\woodhouse\\coverage\\readers\\phpunittextreader::readpercentage","icecave\\woodhouse\\github\\githubclient::__construct","icecave\\woodhouse\\github\\githubclient::url","icecave\\woodhouse\\github\\githubclient::browser","icecave\\woodhouse\\github\\githubclient::authorizations","icecave\\woodhouse\\github\\githubclient::authorizationsmatching","icecave\\woodhouse\\github\\githubclient::createauthorization","icecave\\woodhouse\\github\\githubclient::deleteauthorization","icecave\\woodhouse\\github\\githubclientfactory::__construct","icecave\\woodhouse\\github\\githubclientfactory::useragent","icecave\\woodhouse\\github\\githubclientfactory::setuseragent","icecave\\woodhouse\\github\\githubclientfactory::cacertificatepath","icecave\\woodhouse\\github\\githubclientfactory::create","icecave\\woodhouse\\git\\git::__construct","icecave\\woodhouse\\git\\git::setoutputfilter","icecave\\woodhouse\\git\\git::clonerepo","icecave\\woodhouse\\git\\git::checkout","icecave\\woodhouse\\git\\git::add","icecave\\woodhouse\\git\\git::remove","icecave\\woodhouse\\git\\git::diff","icecave\\woodhouse\\git\\git::commit","icecave\\woodhouse\\git\\git::push","icecave\\woodhouse\\git\\git::pull","icecave\\woodhouse\\git\\git::setconfig","icecave\\woodhouse\\git\\git::executable","icecave\\woodhouse\\git\\git::execute","icecave\\woodhouse\\publisher\\abstractpublisher::__construct","icecave\\woodhouse\\publisher\\abstractpublisher::add","icecave\\woodhouse\\publisher\\abstractpublisher::remove","icecave\\woodhouse\\publisher\\abstractpublisher::clear","icecave\\woodhouse\\publisher\\abstractpublisher::contentpaths","icecave\\woodhouse\\publisher\\githubpublisher::__construct","icecave\\woodhouse\\publisher\\githubpublisher::filesystem","icecave\\woodhouse\\publisher\\githubpublisher::git","icecave\\woodhouse\\publisher\\githubpublisher::publish","icecave\\woodhouse\\publisher\\githubpublisher::dryrun","icecave\\woodhouse\\publisher\\githubpublisher::repository","icecave\\woodhouse\\publisher\\githubpublisher::setrepository","icecave\\woodhouse\\publisher\\githubpublisher::repositoryurl","icecave\\woodhouse\\publisher\\githubpublisher::branch","icecave\\woodhouse\\publisher\\githubpublisher::setbranch","icecave\\woodhouse\\publisher\\githubpublisher::commitmessage","icecave\\woodhouse\\publisher\\githubpublisher::setcommitmessage","icecave\\woodhouse\\publisher\\githubpublisher::authtoken","icecave\\woodhouse\\publisher\\githubpublisher::setauthtoken","icecave\\woodhouse\\publisher\\publisherinterface::add","icecave\\woodhouse\\publisher\\publisherinterface::remove","icecave\\woodhouse\\publisher\\publisherinterface::clear","icecave\\woodhouse\\publisher\\publisherinterface::publish","icecave\\woodhouse\\publisher\\publisherinterface::dryrun"],
        'info': [["Icecave","","Icecave.html","","",3],["Icecave\\Woodhouse","","Icecave\/Woodhouse.html","","",3],["Icecave\\Woodhouse\\BuildStatus","","Icecave\/Woodhouse\/BuildStatus.html","","",3],["Icecave\\Woodhouse\\BuildStatus\\Readers","","Icecave\/Woodhouse\/BuildStatus\/Readers.html","","",3],["Icecave\\Woodhouse\\Console","","Icecave\/Woodhouse\/Console.html","","",3],["Icecave\\Woodhouse\\Console\\Command","","Icecave\/Woodhouse\/Console\/Command.html","","",3],["Icecave\\Woodhouse\\Console\\Command\\GitHub","","Icecave\/Woodhouse\/Console\/Command\/GitHub.html","","",3],["Icecave\\Woodhouse\\Console\\Helper","","Icecave\/Woodhouse\/Console\/Helper.html","","",3],["Icecave\\Woodhouse\\Coverage","","Icecave\/Woodhouse\/Coverage.html","","",3],["Icecave\\Woodhouse\\Coverage\\Readers","","Icecave\/Woodhouse\/Coverage\/Readers.html","","",3],["Icecave\\Woodhouse\\Git","","Icecave\/Woodhouse\/Git.html","","",3],["Icecave\\Woodhouse\\GitHub","","Icecave\/Woodhouse\/GitHub.html","","",3],["Icecave\\Woodhouse\\Publisher","","Icecave\/Woodhouse\/Publisher.html","","",3],["BuildStatus","Icecave\\Woodhouse\\BuildStatus","Icecave\/Woodhouse\/BuildStatus\/BuildStatus.html"," < Enumeration","",1],["CommandLineReader","Icecave\\Woodhouse\\BuildStatus\\Readers","Icecave\/Woodhouse\/BuildStatus\/Readers\/CommandLineReader.html","","",1],["JUnitReader","Icecave\\Woodhouse\\BuildStatus\\Readers","Icecave\/Woodhouse\/BuildStatus\/Readers\/JUnitReader.html","","",1],["PhpUnitJsonReader","Icecave\\Woodhouse\\BuildStatus\\Readers","Icecave\/Woodhouse\/BuildStatus\/Readers\/PhpUnitJsonReader.html","","",1],["TapReader","Icecave\\Woodhouse\\BuildStatus\\Readers","Icecave\/Woodhouse\/BuildStatus\/Readers\/TapReader.html","","",1],["StatusImageSelector","Icecave\\Woodhouse\\BuildStatus","Icecave\/Woodhouse\/BuildStatus\/StatusImageSelector.html","","",1],["StatusReaderFactory","Icecave\\Woodhouse\\BuildStatus","Icecave\/Woodhouse\/BuildStatus\/StatusReaderFactory.html","","",1],["StatusReaderInterface","Icecave\\Woodhouse\\BuildStatus","Icecave\/Woodhouse\/BuildStatus\/StatusReaderInterface.html","","",1],["Application","Icecave\\Woodhouse\\Console","Icecave\/Woodhouse\/Console\/Application.html"," < Application","",1],["AbstractGitHubCommand","Icecave\\Woodhouse\\Console\\Command\\GitHub","Icecave\/Woodhouse\/Console\/Command\/GitHub\/AbstractGitHubCommand.html"," < Command","",1],["CreateAuthorizationCommand","Icecave\\Woodhouse\\Console\\Command\\GitHub","Icecave\/Woodhouse\/Console\/Command\/GitHub\/CreateAuthorizationCommand.html"," < AbstractGitHubCommand","",1],["DeleteAuthorizationCommand","Icecave\\Woodhouse\\Console\\Command\\GitHub","Icecave\/Woodhouse\/Console\/Command\/GitHub\/DeleteAuthorizationCommand.html"," < AbstractGitHubCommand","",1],["ListAuthorizationsCommand","Icecave\\Woodhouse\\Console\\Command\\GitHub","Icecave\/Woodhouse\/Console\/Command\/GitHub\/ListAuthorizationsCommand.html"," < AbstractGitHubCommand","",1],["PublishCommand","Icecave\\Woodhouse\\Console\\Command","Icecave\/Woodhouse\/Console\/Command\/PublishCommand.html"," < Command","",1],["HiddenInputHelper","Icecave\\Woodhouse\\Console\\Helper","Icecave\/Woodhouse\/Console\/Helper\/HiddenInputHelper.html"," < Helper","",1],["CoverageImageSelector","Icecave\\Woodhouse\\Coverage","Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html","","",1],["CoverageReaderFactory","Icecave\\Woodhouse\\Coverage","Icecave\/Woodhouse\/Coverage\/CoverageReaderFactory.html","","",1],["CoverageReaderInterface","Icecave\\Woodhouse\\Coverage","Icecave\/Woodhouse\/Coverage\/CoverageReaderInterface.html","","",1],["CommandLineReader","Icecave\\Woodhouse\\Coverage\\Readers","Icecave\/Woodhouse\/Coverage\/Readers\/CommandLineReader.html","","",1],["PhpUnitTextReader","Icecave\\Woodhouse\\Coverage\\Readers","Icecave\/Woodhouse\/Coverage\/Readers\/PhpUnitTextReader.html","","",1],["GitHubClient","Icecave\\Woodhouse\\GitHub","Icecave\/Woodhouse\/GitHub\/GitHubClient.html","","",1],["GitHubClientFactory","Icecave\\Woodhouse\\GitHub","Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html","","",1],["Git","Icecave\\Woodhouse\\Git","Icecave\/Woodhouse\/Git\/Git.html","","",1],["PackageInfo","Icecave\\Woodhouse","Icecave\/Woodhouse\/PackageInfo.html","","",1],["AbstractPublisher","Icecave\\Woodhouse\\Publisher","Icecave\/Woodhouse\/Publisher\/AbstractPublisher.html","","",1],["GitHubPublisher","Icecave\\Woodhouse\\Publisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html"," < AbstractPublisher","",1],["PublisherInterface","Icecave\\Woodhouse\\Publisher","Icecave\/Woodhouse\/Publisher\/PublisherInterface.html","","",1],["CommandLineReader::__construct","Icecave\\Woodhouse\\BuildStatus\\Readers\\CommandLineReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/CommandLineReader.html#method___construct","(string $buildStatus)","",2],["CommandLineReader::readStatus","Icecave\\Woodhouse\\BuildStatus\\Readers\\CommandLineReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/CommandLineReader.html#method_readStatus","()","",2],["JUnitReader::__construct","Icecave\\Woodhouse\\BuildStatus\\Readers\\JUnitReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/JUnitReader.html#method___construct","(string $reportPath, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["JUnitReader::readStatus","Icecave\\Woodhouse\\BuildStatus\\Readers\\JUnitReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/JUnitReader.html#method_readStatus","()","",2],["PhpUnitJsonReader::__construct","Icecave\\Woodhouse\\BuildStatus\\Readers\\PhpUnitJsonReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/PhpUnitJsonReader.html#method___construct","(string $reportPath, <abbr title=\"Icecave\\Duct\\Parser\">Parser<\/abbr> $parser = null, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["PhpUnitJsonReader::readStatus","Icecave\\Woodhouse\\BuildStatus\\Readers\\PhpUnitJsonReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/PhpUnitJsonReader.html#method_readStatus","()","",2],["TapReader::__construct","Icecave\\Woodhouse\\BuildStatus\\Readers\\TapReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/TapReader.html#method___construct","(string $reportPath, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["TapReader::readStatus","Icecave\\Woodhouse\\BuildStatus\\Readers\\TapReader","Icecave\/Woodhouse\/BuildStatus\/Readers\/TapReader.html#method_readStatus","()","",2],["StatusImageSelector::imageFilename","Icecave\\Woodhouse\\BuildStatus\\StatusImageSelector","Icecave\/Woodhouse\/BuildStatus\/StatusImageSelector.html#method_imageFilename","(<a href=\"Icecave\/Woodhouse\/BuildStatus\/BuildStatus.html\"><abbr title=\"Icecave\\Woodhouse\\BuildStatus\\BuildStatus\">BuildStatus<\/abbr><\/a> $status)","",2],["StatusReaderFactory::__construct","Icecave\\Woodhouse\\BuildStatus\\StatusReaderFactory","Icecave\/Woodhouse\/BuildStatus\/StatusReaderFactory.html#method___construct","(<abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["StatusReaderFactory::supportedTypes","Icecave\\Woodhouse\\BuildStatus\\StatusReaderFactory","Icecave\/Woodhouse\/BuildStatus\/StatusReaderFactory.html#method_supportedTypes","()","",2],["StatusReaderFactory::create","Icecave\\Woodhouse\\BuildStatus\\StatusReaderFactory","Icecave\/Woodhouse\/BuildStatus\/StatusReaderFactory.html#method_create","(string $type, string $argument)","",2],["StatusReaderInterface::readStatus","Icecave\\Woodhouse\\BuildStatus\\StatusReaderInterface","Icecave\/Woodhouse\/BuildStatus\/StatusReaderInterface.html#method_readStatus","()","",2],["Application::__construct","Icecave\\Woodhouse\\Console\\Application","Icecave\/Woodhouse\/Console\/Application.html#method___construct","(string $vendorPath)","",2],["Application::vendorPath","Icecave\\Woodhouse\\Console\\Application","Icecave\/Woodhouse\/Console\/Application.html#method_vendorPath","()","",2],["AbstractGitHubCommand::__construct","Icecave\\Woodhouse\\Console\\Command\\GitHub\\AbstractGitHubCommand","Icecave\/Woodhouse\/Console\/Command\/GitHub\/AbstractGitHubCommand.html#method___construct","(<a href=\"Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html\"><abbr title=\"Icecave\\Woodhouse\\GitHub\\GitHubClientFactory\">GitHubClientFactory<\/abbr><\/a> $clientFactory = null)","",2],["AbstractGitHubCommand::clientFactory","Icecave\\Woodhouse\\Console\\Command\\GitHub\\AbstractGitHubCommand","Icecave\/Woodhouse\/Console\/Command\/GitHub\/AbstractGitHubCommand.html#method_clientFactory","()","",2],["AbstractGitHubCommand::setApplication","Icecave\\Woodhouse\\Console\\Command\\GitHub\\AbstractGitHubCommand","Icecave\/Woodhouse\/Console\/Command\/GitHub\/AbstractGitHubCommand.html#method_setApplication","(<abbr title=\"Symfony\\Component\\Console\\Application\">Application<\/abbr> $application = null)","",2],["CreateAuthorizationCommand::__construct","Icecave\\Woodhouse\\Console\\Command\\GitHub\\CreateAuthorizationCommand","Icecave\/Woodhouse\/Console\/Command\/GitHub\/CreateAuthorizationCommand.html#method___construct","(<a href=\"Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html\"><abbr title=\"Icecave\\Woodhouse\\GitHub\\GitHubClientFactory\">GitHubClientFactory<\/abbr><\/a> $clientFactory = null)","",2],["DeleteAuthorizationCommand::__construct","Icecave\\Woodhouse\\Console\\Command\\GitHub\\DeleteAuthorizationCommand","Icecave\/Woodhouse\/Console\/Command\/GitHub\/DeleteAuthorizationCommand.html#method___construct","(<a href=\"Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html\"><abbr title=\"Icecave\\Woodhouse\\GitHub\\GitHubClientFactory\">GitHubClientFactory<\/abbr><\/a> $clientFactory = null)","",2],["ListAuthorizationsCommand::__construct","Icecave\\Woodhouse\\Console\\Command\\GitHub\\ListAuthorizationsCommand","Icecave\/Woodhouse\/Console\/Command\/GitHub\/ListAuthorizationsCommand.html#method___construct","(<a href=\"Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html\"><abbr title=\"Icecave\\Woodhouse\\GitHub\\GitHubClientFactory\">GitHubClientFactory<\/abbr><\/a> $clientFactory = null)","",2],["PublishCommand::__construct","Icecave\\Woodhouse\\Console\\Command\\PublishCommand","Icecave\/Woodhouse\/Console\/Command\/PublishCommand.html#method___construct","(<a href=\"Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html\"><abbr title=\"Icecave\\Woodhouse\\Publisher\\GitHubPublisher\">GitHubPublisher<\/abbr><\/a> $publisher = null, <a href=\"Icecave\/Woodhouse\/BuildStatus\/StatusReaderFactory.html\"><abbr title=\"Icecave\\Woodhouse\\BuildStatus\\StatusReaderFactory\">StatusReaderFactory<\/abbr><\/a> $statusReaderFactory = null, <a href=\"Icecave\/Woodhouse\/BuildStatus\/StatusImageSelector.html\"><abbr title=\"Icecave\\Woodhouse\\BuildStatus\\StatusImageSelector\">StatusImageSelector<\/abbr><\/a> $statusImageSelector = null, <a href=\"Icecave\/Woodhouse\/Coverage\/CoverageReaderFactory.html\"><abbr title=\"Icecave\\Woodhouse\\Coverage\\CoverageReaderFactory\">CoverageReaderFactory<\/abbr><\/a> $coverageReaderFactory = null, <a href=\"Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html\"><abbr title=\"Icecave\\Woodhouse\\Coverage\\CoverageImageSelector\">CoverageImageSelector<\/abbr><\/a> $coverageImageSelector = null, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["PublishCommand::resolveThemes","Icecave\\Woodhouse\\Console\\Command\\PublishCommand","Icecave\/Woodhouse\/Console\/Command\/PublishCommand.html#method_resolveThemes","(<abbr title=\"Symfony\\Component\\Console\\Input\\InputInterface\">InputInterface<\/abbr> $input)","",2],["PublishCommand::enqueueImages","Icecave\\Woodhouse\\Console\\Command\\PublishCommand","Icecave\/Woodhouse\/Console\/Command\/PublishCommand.html#method_enqueueImages","(array $themes, string $targetPath, string $category, string $filename)","",2],["HiddenInputHelper::__construct","Icecave\\Woodhouse\\Console\\Helper\\HiddenInputHelper","Icecave\/Woodhouse\/Console\/Helper\/HiddenInputHelper.html#method___construct","(string|null $hiddenInputPath = null, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["HiddenInputHelper::getName","Icecave\\Woodhouse\\Console\\Helper\\HiddenInputHelper","Icecave\/Woodhouse\/Console\/Helper\/HiddenInputHelper.html#method_getName","()","",2],["HiddenInputHelper::hiddenInputPath","Icecave\\Woodhouse\\Console\\Helper\\HiddenInputHelper","Icecave\/Woodhouse\/Console\/Helper\/HiddenInputHelper.html#method_hiddenInputPath","()","",2],["HiddenInputHelper::askHiddenResponse","Icecave\\Woodhouse\\Console\\Helper\\HiddenInputHelper","Icecave\/Woodhouse\/Console\/Helper\/HiddenInputHelper.html#method_askHiddenResponse","(<abbr title=\"Symfony\\Component\\Console\\Output\\OutputInterface\">OutputInterface<\/abbr> $output, string|array $question)","",2],["CoverageImageSelector::__construct","Icecave\\Woodhouse\\Coverage\\CoverageImageSelector","Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html#method___construct","(integer $increments = 5)","",2],["CoverageImageSelector::roundPercentage","Icecave\\Woodhouse\\Coverage\\CoverageImageSelector","Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html#method_roundPercentage","(<abbr title=\"Icecave\\Woodhouse\\Coverage\\float\">float<\/abbr> $percentage)","",2],["CoverageImageSelector::imageFilename","Icecave\\Woodhouse\\Coverage\\CoverageImageSelector","Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html#method_imageFilename","(<abbr title=\"Icecave\\Woodhouse\\Coverage\\float\">float<\/abbr> $percentage)","",2],["CoverageImageSelector::errorImageFilename","Icecave\\Woodhouse\\Coverage\\CoverageImageSelector","Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html#method_errorImageFilename","()","",2],["CoverageImageSelector::unknownImageFilename","Icecave\\Woodhouse\\Coverage\\CoverageImageSelector","Icecave\/Woodhouse\/Coverage\/CoverageImageSelector.html#method_unknownImageFilename","()","",2],["CoverageReaderFactory::__construct","Icecave\\Woodhouse\\Coverage\\CoverageReaderFactory","Icecave\/Woodhouse\/Coverage\/CoverageReaderFactory.html#method___construct","(<abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["CoverageReaderFactory::supportedTypes","Icecave\\Woodhouse\\Coverage\\CoverageReaderFactory","Icecave\/Woodhouse\/Coverage\/CoverageReaderFactory.html#method_supportedTypes","()","",2],["CoverageReaderFactory::create","Icecave\\Woodhouse\\Coverage\\CoverageReaderFactory","Icecave\/Woodhouse\/Coverage\/CoverageReaderFactory.html#method_create","(string $type, string $argument)","",2],["CoverageReaderInterface::readPercentage","Icecave\\Woodhouse\\Coverage\\CoverageReaderInterface","Icecave\/Woodhouse\/Coverage\/CoverageReaderInterface.html#method_readPercentage","()","",2],["CommandLineReader::__construct","Icecave\\Woodhouse\\Coverage\\Readers\\CommandLineReader","Icecave\/Woodhouse\/Coverage\/Readers\/CommandLineReader.html#method___construct","(<abbr title=\"Icecave\\Woodhouse\\Coverage\\Readers\\numeric\">numeric<\/abbr> $percentage)","",2],["CommandLineReader::readPercentage","Icecave\\Woodhouse\\Coverage\\Readers\\CommandLineReader","Icecave\/Woodhouse\/Coverage\/Readers\/CommandLineReader.html#method_readPercentage","()","",2],["PhpUnitTextReader::__construct","Icecave\\Woodhouse\\Coverage\\Readers\\PhpUnitTextReader","Icecave\/Woodhouse\/Coverage\/Readers\/PhpUnitTextReader.html#method___construct","(string $reportPath, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["PhpUnitTextReader::readPercentage","Icecave\\Woodhouse\\Coverage\\Readers\\PhpUnitTextReader","Icecave\/Woodhouse\/Coverage\/Readers\/PhpUnitTextReader.html#method_readPercentage","()","",2],["GitHubClient::__construct","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method___construct","(string|null $url = null, <abbr title=\"Buzz\\Browser\">Browser<\/abbr> $browser = null)","",2],["GitHubClient::url","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method_url","()","",2],["GitHubClient::browser","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method_browser","()","",2],["GitHubClient::authorizations","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method_authorizations","()","",2],["GitHubClient::authorizationsMatching","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method_authorizationsMatching","(string|null $namePattern = null, string|null $urlPattern = null)","",2],["GitHubClient::createAuthorization","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method_createAuthorization","(array $scopes = array(), string|null $note = null, string|null $noteUrl = null)","",2],["GitHubClient::deleteAuthorization","Icecave\\Woodhouse\\GitHub\\GitHubClient","Icecave\/Woodhouse\/GitHub\/GitHubClient.html#method_deleteAuthorization","(integer $id)","",2],["GitHubClientFactory::__construct","Icecave\\Woodhouse\\GitHub\\GitHubClientFactory","Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html#method___construct","(string|null $caCertificatePath = null, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["GitHubClientFactory::userAgent","Icecave\\Woodhouse\\GitHub\\GitHubClientFactory","Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html#method_userAgent","()","",2],["GitHubClientFactory::setUserAgent","Icecave\\Woodhouse\\GitHub\\GitHubClientFactory","Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html#method_setUserAgent","(string|null $userAgent)","",2],["GitHubClientFactory::caCertificatePath","Icecave\\Woodhouse\\GitHub\\GitHubClientFactory","Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html#method_caCertificatePath","()","",2],["GitHubClientFactory::create","Icecave\\Woodhouse\\GitHub\\GitHubClientFactory","Icecave\/Woodhouse\/GitHub\/GitHubClientFactory.html#method_create","(string|null $username = null, string|null $password = null)","",2],["Git::__construct","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method___construct","(<abbr title=\"Symfony\\Component\\Process\\ExecutableFinder\">ExecutableFinder<\/abbr> $executableFinder = null)","",2],["Git::setOutputFilter","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_setOutputFilter","(<abbr title=\"Icecave\\Woodhouse\\Git\\callable\">callable<\/abbr>|null $callback)","",2],["Git::cloneRepo","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_cloneRepo","(string $path, string $url, string|null $branch = null, integer|null $depth = null)","",2],["Git::checkout","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_checkout","(string $branch, boolean $orphan = false)","",2],["Git::add","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_add","(string $path, boolean $force = true)","",2],["Git::remove","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_remove","(string $path, boolean $force = true)","",2],["Git::diff","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_diff","(boolean $diffStagedFiles = false)","",2],["Git::commit","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_commit","(string $message)","",2],["Git::push","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_push","(string $remote = &#039;origin&#039;, string|null $branch = null)","",2],["Git::pull","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_pull","()","",2],["Git::setConfig","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_setConfig","(string $key, <abbr title=\"Icecave\\Woodhouse\\Git\\stringable\">stringable<\/abbr> $value)","",2],["Git::executable","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_executable","()","",2],["Git::execute","Icecave\\Woodhouse\\Git\\Git","Icecave\/Woodhouse\/Git\/Git.html#method_execute","(array $arguments)","",2],["AbstractPublisher::__construct","Icecave\\Woodhouse\\Publisher\\AbstractPublisher","Icecave\/Woodhouse\/Publisher\/AbstractPublisher.html#method___construct","()","",2],["AbstractPublisher::add","Icecave\\Woodhouse\\Publisher\\AbstractPublisher","Icecave\/Woodhouse\/Publisher\/AbstractPublisher.html#method_add","(string $sourcePath, string $targetPath)","Enqueue content to be published.",2],["AbstractPublisher::remove","Icecave\\Woodhouse\\Publisher\\AbstractPublisher","Icecave\/Woodhouse\/Publisher\/AbstractPublisher.html#method_remove","(string $sourcePath)","Remove enqueued content at $sourcePath.",2],["AbstractPublisher::clear","Icecave\\Woodhouse\\Publisher\\AbstractPublisher","Icecave\/Woodhouse\/Publisher\/AbstractPublisher.html#method_clear","()","Clear all enqueued content.",2],["AbstractPublisher::contentPaths","Icecave\\Woodhouse\\Publisher\\AbstractPublisher","Icecave\/Woodhouse\/Publisher\/AbstractPublisher.html#method_contentPaths","()","",2],["GitHubPublisher::__construct","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method___construct","(<a href=\"Icecave\/Woodhouse\/Git\/Git.html\"><abbr title=\"Icecave\\Woodhouse\\Git\\Git\">Git<\/abbr><\/a> $git = null, <abbr title=\"Symfony\\Component\\Filesystem\\Filesystem\">Filesystem<\/abbr> $fileSystem = null, <abbr title=\"Icecave\\Isolator\\Isolator\">Isolator<\/abbr> $isolator = null)","",2],["GitHubPublisher::fileSystem","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_fileSystem","()","",2],["GitHubPublisher::git","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_git","()","",2],["GitHubPublisher::publish","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_publish","()","Publish enqueued content.",2],["GitHubPublisher::dryRun","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_dryRun","()","Perform a publication dry-run.",2],["GitHubPublisher::repository","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_repository","()","",2],["GitHubPublisher::setRepository","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_setRepository","(string $repository)","",2],["GitHubPublisher::repositoryUrl","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_repositoryUrl","()","",2],["GitHubPublisher::branch","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_branch","()","",2],["GitHubPublisher::setBranch","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_setBranch","(string $branch)","",2],["GitHubPublisher::commitMessage","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_commitMessage","()","",2],["GitHubPublisher::setCommitMessage","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_setCommitMessage","(string $commitMessage)","",2],["GitHubPublisher::authToken","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_authToken","()","",2],["GitHubPublisher::setAuthToken","Icecave\\Woodhouse\\Publisher\\GitHubPublisher","Icecave\/Woodhouse\/Publisher\/GitHubPublisher.html#method_setAuthToken","(string|null $authToken)","",2],["PublisherInterface::add","Icecave\\Woodhouse\\Publisher\\PublisherInterface","Icecave\/Woodhouse\/Publisher\/PublisherInterface.html#method_add","(string $sourcePath, string $targetPath)","Enqueue content to be published.",2],["PublisherInterface::remove","Icecave\\Woodhouse\\Publisher\\PublisherInterface","Icecave\/Woodhouse\/Publisher\/PublisherInterface.html#method_remove","(string $sourcePath)","Remove enqueued content at $sourcePath.",2],["PublisherInterface::clear","Icecave\\Woodhouse\\Publisher\\PublisherInterface","Icecave\/Woodhouse\/Publisher\/PublisherInterface.html#method_clear","()","Clear all enqueued content.",2],["PublisherInterface::publish","Icecave\\Woodhouse\\Publisher\\PublisherInterface","Icecave\/Woodhouse\/Publisher\/PublisherInterface.html#method_publish","()","Publish enqueued content.",2],["PublisherInterface::dryRun","Icecave\\Woodhouse\\Publisher\\PublisherInterface","Icecave\/Woodhouse\/Publisher\/PublisherInterface.html#method_dryRun","()","Perform a publication dry-run.",2]]
    }
}
search_data['index']['longSearchIndex'] = search_data['index']['searchIndex']