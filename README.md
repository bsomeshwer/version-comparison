# version-comparison
This Laravel package compares two version strings and gives the Boolean result. This package also resolves version expressions like (($v > 1.24.0) &amp;&amp; ($v &lt; 1.25.1.0)) || ($v == 1.26 || $v == 1.27) where $v must be substituted with the version number to be compared.
Hence the package can be used for version expressions evaluation.

# Installation
 To download the latest version type the following line in terminal and then press enter.
    
    composer require someshwer/version-comparison
 
 And then add the following service provider to <b>providers</b> array in config/app.php configuration file.
 
    Ex: 'providers' => [
            ... ,
            ... ,
            Someshwer\VersionComparison\VersionComparisonServiceProvider::class
        ]; 
        
  And add the following alias to <b>aliases</b> array in config/app.php configuration file.
  
    Ex: 'aliases' => [
            ... ,
            .... ,
            'VersionComparator' => Someshwer\VersionComparison\Facades\VersionComparator::class
        ]; 
       
  Now just simly publish the service provider by running following command in terminal. (Optional Step)
  
        $ php artisan vendor:publish --provider="Someshwer\VersionComparison\VersionComparisonServiceProvider"
        
        
        That's all! You are done with package installation...
        
        
  # Usage:
  1. VersionComparator::compare(): 
  This compares two version strings using specified operator and gives the bollean result. It takes three parameters:
    
    VersionComparator::compare(Version String, Operator, Version String); // compare(Version1, Operator, Version2);
    
    a. Version number (First Parameter)
    b. Operator (Second Parameter)
    c. Version number (Third Paramaeter)
    
    Ex: VersionComparator::compare('1.2.5', '>', '1.2.2'); // Gives the boolean result either TRUE or FALSE. 
    For this comparison result is TRUE.
    
  2. VersionComparator::evaluate()": 
  This method usually evaluates version expression of the following form: 
  (("1.2" > "1.24.0") && ("1.2" < "1.25.1.0")) || ("1.2" == "1.26" || "1.2" == "1.27")
  It takes only one argument that is expression of the above type. 
 
    VersionComparator::evaluate("Version Expression"); 
    
    Ex: VersionComparator::evaluate((("1.2" > "1.24.0") && ("1.2" < "1.25.1.0")) || ("1.2" == "1.26" || "1.2" == "1.27"));
    // Gives boolean result either TRUE or FALSE
    
  3. VersionComparator::substituteThenEvaluate(): 
  This method usually evaluates version expressions of the following form:
  (($v > 1.24.0) && ($v < 1.25.1.0)) || ($v == 1.26 || $v == 1.27)
    
    VersionComparator::substituteThenEvaluate("Version Number", "Version Expression");
    
   
 
  
  
