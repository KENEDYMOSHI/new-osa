1. Target directory Modules/MetrologicalSupervision(IMPORTANT)
2. each file must be in the same directory following the MVC structure
3.  All the Views uses the coreLayout
4.  in the side menu there is there is Metrological supervision dropdown inside it there is settings 
     so the settings should have a route group metrology/settings
     and the view should be in the settings directory
5.  all table must hav a prefix metro_ and column name must be in camelCase
6.  models return object
7.  in controller there should be a render method which returns data in Heredoc syntax and inject into html

8. always add comments to the code

9. Always use fetch api with async and await and try catch avoid using $ajax, only use jquery for minor stuff like select2 modals and dataTables use vanilla js most of the time
10. Always add try catch in controller methods for better error handling