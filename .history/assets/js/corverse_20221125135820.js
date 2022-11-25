

$(document).ready(function() {

	//Button for profile post
	$('#submit_profile_post').click(function(){
		
		$.ajax({
			type: "POST",
			url: "includes/handlers/ajax_submit_profile_post.php",
			data: $('form.profile_post').serialize(),
			success: function(msg) {
				$("#post_form").modal('hide');
				location.reload();
			},
			error: function() {
				alert('Failure');
			}
		});

	});


});


$(document).click(function(e){

	if(e.target.class != "search_results" && e.target.id != "search_text_input") {

		$(".search_results").html("");
		$('.search_results_footer').html("");
		$('.search_results_footer').toggleClass("search_results_footer_empty");
		$('.search_results_footer').toggleClass("search_results_footer");
	}

	if(e.target.className != "dropdown_data_window") {

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height" : "0px"});
	}
});


function getUsers(value, user) {
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	});
}

function getDropdownData(user, type) {

	if($(".dropdown_data_window").css("height") == "0px") {

		var pageName;

		if(type == 'notification') {
			pageName = "ajax_load_notifications.php";
			$("span").remove("#unread_notification");
		}
		else if (type == 'message') {
			pageName = "ajax_load_messages.php";
			$("span").remove("#unread_message");
		}

		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cache: false,

			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding" : "0px", "height": "fit-content"});
				$("#dropdown_data_type").val(type);
			}

		});

	}
	else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
	}

}

	function getLiveSearchUsers(value, user) {
	
		$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn: user}, function(data) {
	
			if($(".search_results_footer_empty")[0]) {
				$(".search_results_footer_empty").toggleClass("search_results_footer");
				$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
			}
	
			$('.search_results').html(data);
			$('.search_results_footer').html("<a class='view_results_text' href='search.php?q=" + value + "'>View all results</a>");
	
			if(value.length == 0) {
				$('.search_results_footer').html("");
				$('.search_results_footer').toggleClass("search_results_footer_empty");
				$('.search_results_footer').toggleClass("search_results_footer");
			}
	
		});
	}


   $(function(){
 
    $("[data-fancybox]").fancybox();
 
    $(".icons .projectLink").on("click", function(e){
 
      const imagePath = this.id;
      
      bootbox.confirm("Delete image?", function(result){
        if(result) {
    
          $.post("includes/handlers/delete_gallery_image.php", {imagePath:imagePath, userLoggedIn:userLoggedIn}, function(data){
    
            setTimeout(function(){
              if(window.location.hash === "")
                    window.location.hash = "#gallery_div";
              location.reload();
            }, 300);
            
          });
        }
        
      });
 
    });
 
  });
 
$(function(){
    $("#submitGallery").on("click", function(e){
        if($("#galleryUpload").val() === "") {
            e.preventDefault();
            confirm("Please select an image first");
            return;
        }
    })
  })


  function userTag(user) {  
	$.post("includes/handlers/ajax_user_tag.php", {userLoggedIn:user}, function(data){
	$('.tag_results').html(data);
	});
   }
	
   function textTag() { 
	$('.displayTag a').click(function(a){
		   var username = $(this).attr('href');
		   var quotesName = JSON.stringify(username);
		   var name = JSON.parse(quotesName);
	
		   var $txt = $("#post_text");
		   var caretPos = $txt[0].selectionStart;
		   var textAreaTxt = $txt.val();
	
		   $txt.val(textAreaTxt.substring(0, caretPos) + name + textAreaTxt.substring(caretPos) );
		   $('.tag_results').html("");
	
		   return false;
		 });
   }


  // Get the theme toggle input
  const themeToggle = document.querySelector(
	'.theme-switch input[type="checkbox"]'
  );
  // Function that will switch the theme based on the if the theme toggle is checked or not
  function switchTheme(e) {
	if (e.target.checked) {
	  document.documentElement.setAttribute("data-theme", "dark");
	} else {
	  document.documentElement.setAttribute("data-theme", "light");
	}
  }
  // Add an event listener to the theme toggle, which will switch the theme
  themeToggle.addEventListener("change", switchTheme, false);
  
  
  function switchTheme(e) {
	if (e.target.checked) {
	  document.documentElement.setAttribute("data-theme", "dark");
	  
	  // Set the user's theme preference to dark
	  localStorage.setItem("theme", "dark");
	} else {
	  document.documentElement.setAttribute("data-theme", "light");
	  
	  // Set the user's theme preference to light
	  localStorage.setItem("theme", "light");
	}
  }
  
  // Get the current theme from local storage
  const currentTheme = localStorage.getItem("theme");
  // If the current local storage item can be found
  if (currentTheme) {
	// Set the body data-theme attribute to match the local storage item
	document.documentElement.setAttribute("data-theme", currentTheme);
  // If the current theme is dark, check the theme toggle
	if (currentTheme === "dark") {
	  themeToggle.checked = true;
	}
  }