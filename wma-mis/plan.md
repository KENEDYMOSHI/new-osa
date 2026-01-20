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


In the system, meter fees shall be determined based on the selected meter category, with each category having a predefined base amount. The final payable fee for each application shall be calculated by multiplying the base amount by a fixed factor of 5, and the result of this multiplication shall be the final fee. The quantity of meters entered shall be used for record and reference purposes only and shall not affect the fee calculation.

When Water Meter is selected, the system shall apply a base amount of 10,000 and calculate the final fee as 10,000 × 5. When Flow Meter is selected, the system shall apply a base amount of 500,000 and calculate the final fee as 500,000 × 5. For Bulk Flow Meter, the system shall apply a base amount of 2,500,000 and calculate the final fee as 2,500,000 × 5. When Electrical Meter is selected, the system shall apply a base amount of 10,000 and calculate the final fee as 10,000 × 5.

The system shall automatically identify the selected meter category, apply the corresponding calculation rule, and display the calculated fee to the user. Any change to the meter category shall immediately trigger a recalculation of the fee to ensure accuracy and consistency before submission.