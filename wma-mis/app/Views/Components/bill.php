 <section style="margin:0; padding:0;" id="printingSection">
     <div class="printingContent" style="margin:0; padding:0;">
         <div>
             <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true" style="padding: 0;margin:0;">
                 <div class="modal-dialog modal-xl" role="document">
                     <div class="modal-content">



                         <div class="modal-body-wrapper">
                             <div class="modal-body" id="modal-body">
                                 <div>
                                     <div class="bill-header">
                                         <div class="logo">
                                             <img src="<?= base_url() ?>/assets/images/emblem.png" alt="" />
                                         </div>
                                         <div class="heading4">
                                             <h5 class="text-center"><b>THE UNITED REPUBLIC OF TANZANIA</b></h5>
                                             <h5 class="text-center"><b>MINISTRY OF  INDUSTRY AND TRADE </b></h5>
                                             <h5 class="text-center">WEIGHTS AND MEASURES AGENCY</h5>
                                             <!-- <h5 class="text-center">Government Bill</h5> -->
                                             <p class="text-center" id="heading"></p>
                                         </div>
                                         <div class="logo">
                                             <img class="float-right" src="<?= base_url() ?>/assets/images/wma1.png" alt="" />
                                         </div>
                                     </div>
                                     <hr>
                                     <div id="billDetails">

                                     </div>





                                 </div>
                                 <p class="text-center">Weights And Measures Agency Copyright &copy; <?=date('Y')?> All Rights Reserved</p>
                             </div>
                         </div>
                         <div class="modal-footer">
                             <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                             <button type="button" id="printBtn" class="btn btn-primary print btn-sm"><i class="fal fa-print"></i> Print</button>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>

 <script>
     $(document).on("click", ".print", function() {
         const section = $("#printingSection");
         const modalBody = $("#modal-body").detach();

         const content = $(".printingContent").detach();
         section.append(modalBody);
         window.print();
         section.empty();
         section.append(content);
         $(".modal-body-wrapper").append(modalBody);

         //  function beforePrint() {
         //      console.log('Printing started');
         //  }



     });

     function listenPrint() {
         window.location.reload();

     }



     if (window.matchMedia) {
         var mediaQueryList = window.matchMedia('print');
         mediaQueryList.addListener(function(mql) {
             if (!mql.matches) {
                 listenPrint();
             }
         });
     }


     window.onafterprint = listenPrint;
 </script>