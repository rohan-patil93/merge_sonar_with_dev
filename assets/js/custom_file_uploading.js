  function validationfunction(){
     Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: $("#validation_message").val(),
     });
  }
  function brokenimagesreplace(){
  var images = $('.brockenimage');
  for (var i = 0; i < images.length; i++) {
      images[i].onerror = function() {
       $(this).attr('src', 'uploads/document_icon.png');
       }
    }
  }

        
        Dropzone.autoDiscover = false;
        var name="";
        var applicationTableName = $("#application_table_name").val();
        var transactionId = $("#transaction_id").val();
        var file_size = $("#file_size").val();
        var file_limit = $("#file_limit").val();
		var transaction_number = $("#transaction_number").val();
        window.onload = function () {

            var dropzoneOptions = {
                url:'ajax.php',

                dictDefaultMessage: 'Drop Here!',
                paramName: "file",
                //maxFilesize: 0.5, // MB
                acceptedFiles: ".jpeg,.jpg,.jpeg,.png,.gif,.pdf,.txt,.docx,.doc,.csv,.exe",
                init: function () {
                    
                    this.on("sending", function(file, xhr, formData) {
                        formData.append("page", "imageuploading"); 
                        formData.append("applicationTableName", applicationTableName); 
                        formData.append("transactionId", transactionId );
                        formData.append("file_size", file_size); 
                        formData.append("file_limit", file_limit);
						formData.append("transaction_number", transaction_number);
						formData.append("total_files", $(".dz-remove").length);
                    });
                    
                     this.on("success", function (file, response){
                        console.log(response);
                     	var jsonresponse = JSON.parse(response);
                      //  alert(jsonresponse['status']);
                        if(jsonresponse['status']!="sucess"){
                        
 // swal(''+jsonresponse['status']+'','','error');
    swal({
          icon: 'error',
          title: 'Oops...',
          text: jsonresponse['status'],
       });

                        file['accept']="1";
                           this.removeFile(file);
                        }else{
                            file.previewElement.onclick = function() {
                                window.open('uploads/attachments/'+jsonresponse.filename, '_blank');
                            }
                                file['newfilename']=jsonresponse.filename;
                             file['lastid']=jsonresponse.LASTID;
                          file['accept']="0";
                         // $(".needsclick").css("display","none");
                        }

                    });
 				},
                addRemoveLinks: true,
                removedfile: function(file) {
                var names = file.newfilename; 
                var lastid = file.lastid; 
                var accept = file.accept;
                if(accept==0){
                    $.ajax({
                    type: 'post',
                    url: 'ajax.php',
                	data: "id="+lastid+"&page=fileuploadingdelete&isactive=1",
                    success:function(data){
                        console.log(data);
                       }
                    });
                    deleteTmageTemplates(lastid,names);
                }else{
                   // $(".needsclick").css("display","none");
                }
               // alert($(".dz-remove").length);
                if($(".dz-remove").length==1){
                   // $(".needsclick").css("display","block");
                }
                var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
                }
            };
            var uploader = document.querySelector('#kt_dropzone_2');
            var newDropzone = new Dropzone(uploader, dropzoneOptions);
        };
	function deleteTmageTemplates(lastid,names){
       return  $(".deletesrow").append('<div class="col-md-2 dz-preview dz-processing dz-image-preview dz-success text-center dz-complete divss del'+lastid+'"  style="width: fit-content;"><a class="dleteimages f-14 cursor-pointer" onclick="reused(\''+names+ '\','+lastid+')">Reused</a><div class="dz-image"><a href="uploads/attachments/'+names+'" target="_blank"><img class="brockenimage" src="uploads/attachments/'+names+'" style=" width: 100%;" ></a></div></div>');      
    }
 	function removeattachfile(id,imgname){
      //  alert($(".dz-remove").length);
        if($(".dz-remove").length==1){
            //$(".needsclick").css("display","block");
        }
        $.ajax({
            type: 'POST',
            url: 'ajax.php',
            data: "id="+id+"&page=fileuploadingdelete&isactive=1",
        });
        $('.delete'+id).remove();
        deleteTmageTemplates(id,imgname); 
        brokenimagesreplace();   
    }
	function reused(imgname,id){
        var file_limit = $("#file_limit").val();
        var files  = $(".dz-remove").length;
        var er_message = $("#max_file_upload_message").val();
        if(file_limit < files+1){
             // swal('Uploding limit','','error');
             swal({
          icon: 'error',
          title: 'Oops...',
          text: ''+er_message+'',
       });

        }else{
            $.ajax({
                    type: 'POST',
                url: 'ajax.php',
                data: "id="+id+"&page=fileuploadingdelete&isactive=0",
            });
            //$(".needsclick").css("display","none");
            $('.del'+id).remove();
            $(".digits").append('<div class="delete'+id+' dz-preview dz-processing dz-success dz-complete dz-image-preview ">  <div class="dz-image"><img data-dz-thumbnail="" class="brockenimage" alt="'+imgname+'" style=" height: 120px; width: 100%;" src="uploads/attachments/'+imgname+'"></div>  <div class="dz-details">    <div class="dz-size"></div> <div class="dz-filename"><a href="uploads/attachments/'+imgname+'" target="_blank"><span data-dz-name=""> '+imgname+'</span></a></div>  </div>  <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress="" style="width: 100%;"></span></div>  <div class="dz-error-message"><span data-dz-error-message=""></span></div>  <div class="dz-success-mark">  </div><a class="dz-remove" onclick="removeattachfile('+id+',\''+imgname+ '\')" href="javascript:undefined;" data-dz-remove="">Remove file</a></div>');
            $(".digits").addClass(" dz-clickable dz-started");
            brokenimagesreplace();
        }
    }