var states = {}  

/*
  Function used to compile a post that is visible on the screen
*/
function makepost(title, desc, user, uid, rating, del, postid) {
    var string = ''
    string += '<div class="panel panel-default">'
    if(del)
      string +=     '<div class="panel-heading"><b>'+String(title)+' </b><a class="deleterr" href="#" post-id="'+postid+'"><span class="glyphicon glyphicon-remove btn-danger"></span></a><span class="pull-right"><a href="#" data-value="'+uid+'">'+user+'</a></span></div>'
    else
      string +=     '<div class="panel-heading"><b>'+String(title)+'</b><span class="pull-right"><a href="#" class="otherprof" data-id="'+uid+'">'+user+'</a></span></div>'
    string +=     '<div class="panel-body">'+String(desc)+'<br/><a href="#">Sound File</a></div>'
    string +=     '<div class="panel-footer">'
    string +=        '<button type="button" class="btn btn-primary getcomments" post-id="'+postid+'" data-toggle="modal" data-target="#myModal">Show Comments</button>'
    string +=        '<div class="pull-right" id="rating'+postid+'" style="font-size: 18px  padding: 5px">'+rating+'</div>'
    string +=        '<button type="button" class="btn btn-success btn pull-right rate" post-id="'+postid+'">Rate</button>'
    string +=     '</div>'
    string += '</div>'
    return string
}

function makecomment(uid, username, comment){
  var string = ''
  string += '<div class="panel panel-default">'
  string += '<div class="panel-heading" style="font-size:16px"><b><a href="#" user-id="'+uid+'">'+username+'</a></b></div>'
  string += '<div class="panel-body">'+comment+'</div></div>'
  return string
      
}

/*
  Function called on submit comment event, ajaxes the comment data up to the database
*/
var storeComment = function() {
  var userid = $('#u-id').val()
  var comment = $('#theComment').val()
  var postid = $(this).attr('post-id')
  $.ajax({
    type : 'POST',
    url : '/project/php/govenor.php',
    data : {action : 'new_comment', json : {user : userid, post : postid, content : comment}},
    success : function(wat){
      console.log(wat)
      getComments2(postid)
    }
  })

}

var getComments = function(){
  getComments2($( this ).attr('post-id'))
}


/*
    Function called on comments call event, ajaxes the postid to get the comments
*/
var getComments2 = function(_postid) {
  var postid = _postid    
  $('#addcomment').attr('post-id', postid)
  $.ajax({
    type : 'POST',
    url : "/project/php/govenor.php",
    data : {action : 'get_comments', json : {post_id : postid} },
    success : function(json){
      console.log(json)
      var data = $.parseJSON(json)
      $('#commentsHolder').html('')
      $.each(data, function(key, val){
        $('#commentsHolder').append(makecomment(val['USER_ID'], val['USERNAME'], val['CONTENT']))
      })
    }
  })
}

/*
  Function used to delete the post that had their button pressed. The function is called by the bound event.
*/
var deletepost = function(){
	var postid = $( this ).attr('post-id')
    console.log(postid)
    $.ajax({
      type : 'post',
      url : '/project/php/govenor.php',
      data : {action : 'delete_post', json : {post_id : postid}},
      success : function(hello){
        console.log(hello)
        var userid = $('#u-id').val()
        $( this ).parent().find('.panel').remove()
      }
    })
}

/*
  Function used to have the current user unsubscribe from the user that owns the provided unsubscribe button.
  The function is called by the bound event.
*/
var unsub = function(){
	var userid = $('#u-id').val()
    var otherid = $(this).attr('data-id')
    $.ajax({
        type : 'post',
        url : '/project/php/govenor.php',
        data : {action : 'unsubscribe', json : {suber : userid, subee : otherid}},
        success : function(hi){
          console.log(hi)
          var userid = $('#u-id').val()
          pull_subedto(userid)
          //window.location.replace('http://104.131.97.153/project/index.php')
        }
    })
}

/*
  Function used to have the current user subscribe from the user that owns the provided subscribe button.
  The function is called by the bound event.
*/
var sub = function(){
	var userid = $('#u-id').val()
	var otherid = $(this).attr('data-id')
	$.ajax({
	  type : 'post',
	  url : '/project/php/govenor.php',
	  data : {action : 'subscribe', json : {suber : userid, subee : otherid}},
	  success : function(hi){
	    console.log(hi)
      var userid = $('#u-id').val()
      pull_subedto(userid)
	    //window.location.replace('http://104.131.97.153/project/index.php')
	  }
	})
}

/*
  AJAX call and DOM edit to increment the 
*/
var dorate = function(){
	var postid = $( this ).attr('post-id')
    $.ajax({
      type : 'POST',
      url : '/project/php/govenor.php',
      data : {action : 'rate', json : {action : 'rate', post_id : postid}},
      success : function(woo){
        console.log(woo)
      }
    })
    var old = $('#rate'+postid).html()
    $('#rate'+postid).html(old+1)
}

/*
  AJAX call and page population to add the subscription posts to the page when the subscription event is called
*/
var pull_subs = function(){
	$('#content_head').html('Subscriptions')
	$('#content').html('')
	var userid = $('#u-id').val()
	$.ajax({
		type : 'POST',
		url : '/project/php/govenor.php',
		data : {action : 'get_posts', json : {action : 'subs', user_id : userid}},
		success : function(json){
		  console.log(json)
		  var posts = $.parseJSON(json)
		  $.each(posts, function(key, val){
		    $('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], false, val['POST_ID']))
		  })
		  $('.rate').click(dorate)
		  $('.otherprof').click(pull_profile)
		  $('.subscribe').click(sub)
		  $('.unsubscribe').click(unsub)
      $('.getcomments').on("click", getComments)
    }
  })
}

/*
  AJAX call and page population to add the user's, that was selected, posts when the profile event is called
*/
var pull_profile = function(){
    var userid = $(this).attr('data-id')
    var name = $(this).text()
    var ownerid = $('#u-id').val()
    if(userid === ownerid)
      $('#content_head').html("My Profile")
    else
      $('#content_head').html(name+"'s Profile <a href='#' class='pull-right unsubscribe btn btn-warning' data-id='"+userid+"'>UnSubscribe!</a> <a href='#' class='pull-right subscribe btn btn-success' data-id='"+userid+"'>Subscribe!</a>")
  	$('#content').html('')
  	$.ajax({
		type : 'POST',
		url : '/project/php/govenor.php',
		data : {action : 'get_posts', json : {action : 'user_id', user_id : userid}},
		success : function(json){
			var posts = $.parseJSON(json)
			$.each(posts, function(key, val){
				var userid = $('#u-id').val()
				var bop = false
				if(val['USER_ID'] === userid)
					bop = true
				$('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], bop, val['POST_ID']))
			})
			$('.deleterr').click(deletepost)
  	 	$('.subscribe').click(sub)
  		$('.unsubscribe').click(unsub)
      $('.getcomments').on("click", getComments)
    }
  })
}

/*
  AJAX call and page population of the most popular posts, when document returns ready or the global event is called
*/
var pull_global = function(){
	$('#content_head').html('Global Feed')
	$('#content').html('')
	$.ajax({
		type : 'POST',
		url : '/project/php/govenor.php',
		data : {action : 'get_posts', json : {action : 'recent'}},
		success : function(json){
			var posts = $.parseJSON(json)
			$.each(posts, function(key, val){
				$('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], false, val['POST_ID']))
			})
			$('.rate').click(dorate)
			$('.otherprof').click(pull_profile)
      $('.getcomments').on("click", getComments)
		}
	})
}

/*
  AJAX call and column population of the profiles (with links) the user is subscribed to
*/
var pull_subedto = function(userid){
  $('#bullet').html('')
  $('#bullet').append('<li><h1>Subscriptions</h1></li>')
	$.ajax({
    type : 'post',
    url : '/project/php/govenor.php',
    data : {action : "get_subscriptions", json : {user_id : userid}},
    success : function(subs){
    	var all = $.parseJSON(subs)
  		$.each(all, function(key, val){
  			$('#bullet').append('<li><a href="#" class="otherprof" data-id="'+val['SUBEE']+'">'+val['uname']+'</a></li>')
  		})
		  $('.otherprof').click(pull_profile)
    }
  })
}


////////////////////////////////////////////////////////////////////////

/*
  DOM and files have finished loading
*/
$( document ).ready(function(){
	var userid = $('#u-id').val()
	pull_subedto(userid)
	pull_global()
  $('#addcomment').click(storeComment)

})

// Bind the profile, global, and subscription events to the appropriate functions
$('#profile').click(pull_profile)
$('#global').click(pull_global)
$('#subs').click(pull_subs)

$('a#changepass').click(function(){
   $('div#postfields').html('')
  if(states.chpass){
     $('div#passfields').html('')
     states.chpass = false
  }
  else{
    states.chpass = true
    states.post = false
    $('div#passfields').append('<div class="input-group"><input type="text" class="form-control" id="newpass"><span class="input-group-btn"><button class="btn btn-default" id="runchange" type="button">Update!</button></span></div><!-- /input-group -->')
    $('button#runchange').click(function(){
      var userid = $('#u-id').val()
      console.log(userid)
      var pass = $('#newpass').val()
      $.ajax({
        type  : "POST",
        url   : "/project/php/govenor.php",
        data  : {action : "change_password", json : {user_id : userid, password : pass}},
        success : function(data){
          window.location.replace('http://104.131.97.153/project/index.php?action=signout')
        }
      })
    })
  }
})

/* 
  Jquery event bind on the 'Post a Snippet' button

  If state of post is true then it removes the input fields
  If state of post is false then it shows the input fields
*/
$('a#makepost').click(function(){
  $('div#passfields').html('')
  if(states.post){
     $('div#postfields').html('')
     states.post = false
  }
  else{
    states.chpass = false
    states.post = true
    $('div#postfields').append('<div class="input-group"><input type="text" class="form-control" id="title" placeholder="Title"><textarea placeholder="Description" id="content" width="290px"></textarea><input type="file" name="soundfile" /><button class="btn btn-primary btn-lg btn-block" id="makeapost">Submit!</button></div><!-- /input-group -->')
    
    /*
      Bind the asyncronous post 
    */
    $('button#makeapost').click(function(){
      var userid = $('#u-id').val()
      var title = $('input#title').val()
      var content = $('textarea#content').val()
      $.ajax({
        type : 'POST',
        url : '/project/php/govenor.php',
        data : {action : 'new_post', json : {user_id : userid, sound_id : 'null', title : title, content : content}},
        success : function(data){
            console.log('POST MADE')
            var userid = $('#u-id').val()
            getprofileposts(userid)
            $('div#postfields').html('')
            states.post = false

        }
      })
    })
  }
})