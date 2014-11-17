var states = {};

$('a#makepost').click(function(){
  $('div#passfields').html('');
  if(states.post){
     $('div#postfields').html('');
     states.post = false;
  }
  else{
    states.chpass = false;
    states.post = true;
    $('div#postfields').append('<div class="input-group"><input type="text" class="form-control" id="title" placeholder="Title"><textarea placeholder="Description" id="content" width="290px"></textarea><input type="file" name="soundfile" /><button class="btn btn-primary btn-lg btn-block" id="makeapost">Submit!</button></div><!-- /input-group -->')
    $('button#makeapost').click(function(){
      var userid = $('#u-id').val();
      var title = $('input#title').val();
      var content = $('textarea#content').val();
      $.ajax({
        type : 'POST',
        url : '/project/php/govenor.php',
        data : {action : 'new_post', json : {user_id : userid, sound_id : 'null', title : title, content : content}},
        success : function(data){
            console.log('POST MADE');
            var userid = $('#u-id').val();
            getprofileposts(userid);
            $('div#postfields').html('');
            states.post = false;

        }
      })
    })
  }
})

$('.subscribe').click(function(){
  var userid = $('#u-id').val();
  var otherid = $(this).attr('data-id');
  $.ajax({
      type : 'post',
      url : '/project/php/govenor.php',
      data : {aciton : 'subscribe', json : {suber : userid, subee : otherid}},
      success : function(hi){
        console.log(hi);
        window.location.replace('http://104.131.97.153/project/index.php');
      }
  })
})

$('.unsubscribe').click(function(){
    var userid = $('#u-id').val();
    var otherid = $(this).attr('data-id');
    $.ajax({
        type : 'post',
        url : '/project/php/govenor.php',
        data : {aciton : 'unsubscribe', json : {suber : userid, subee : otherid}},
        success : function(hi){
          console.log(hi);
          window.location.replace('http://104.131.97.153/project/index.php');
        }
    })
})

$('a#changepass').click(function(){
   $('div#postfields').html('');
  if(states.chpass){
     $('div#passfields').html('');
     states.chpass = false;
  }
  else{
    states.chpass = true;
    states.post = false;
    $('div#passfields').append('<div class="input-group"><input type="text" class="form-control" id="newpass"><span class="input-group-btn"><button class="btn btn-default" id="runchange" type="button">Update!</button></span></div><!-- /input-group -->');
    $('button#runchange').click(function(){
      var userid = $('#u-id').val();
      console.log(userid);
      var pass = $('#newpass').val();
      $.ajax({
        type  : "POST",
        url   : "/project/php/govenor.php",
        data  : {action : "change_password", json : {user_id : userid, password : pass}},
        success : function(data){
          window.location.replace('http://104.131.97.153/project/index.php?action=signout');
        }
      })
    })
  }
})

function makepost(title, desc, user, uid, rating, del, postid) {
    var string = '';
    string += '<div class="panel panel-default">';
    if(del)
      string +=     '<div class="panel-heading"><b>'+String(title)+' </b><a class="deleterr" href="#" id="'+postid+'"><span class="glyphicon glyphicon-remove btn-danger"></span></a><span class="pull-right"><a href="#" data-value="'+uid+'">'+user+'</a></span></div>';
    else
      string +=     '<div class="panel-heading"><b>'+String(title)+'</b><span class="pull-right"><a href="#" class="otherprof" data-id="'+uid+'">'+user+'</a></span></div>';
    string +=     '<div class="panel-body">'+String(desc)+'<br/><a href="#">Sound File</a></div>';
    string +=     '<div class="panel-footer">';
    string +=        '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Show Comments</button>';
    string +=        '<div class="pull-right" style="font-size: 18px;  padding: 5px;">'+rating+'</div>'
    string +=        '<button type="button" class="btn btn-success btn pull-right rate" post-id="'+postid+'">Rate</button>';
    string +=     '</div>';
    string += '</div>';
    return string;
}

function getprofileposts(userid) {
  $('#content_head').html('My Profile');
  $('#content').html('');
  $.ajax({
    type : 'POST',
    url : '/project/php/govenor.php',
    data : {action : 'get_posts', json : {action : 'user_id', user_id : userid}},
    success : function(json){
      //console.log(json);
      var posts = $.parseJSON(json);
      $.each(posts, function(key, val){
        var userid = $('#u-id').val();
        var bop = false;
        if(val['USER_ID'] === userid)
          bop = true;
        else
          bop = false;
        $('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], bop, val['POST_ID']));
      })
      $('a.deleterr').click(function(){
        var postid = $( this ).attr('post-id');
        console.log(postid);
        $.ajax({
          type : 'post',
          url : '/project/php/govenor.php',
          data : {action : 'delete_post', json : {post_id : postid}},
          success : function(hello){
            console.log(hello);
            var userid = $('#u-id').val();
            getprofileposts(userid);
          }
        })
      })
      $('.rate').click(function(){
        var postid = $( this ).attr('post-id');
        rate(postid);
        var old = $( this ).val();
        $( this ).val(old+1);
      })
    }
  })

}

$('#subs').click(function(){
  $('#content_head').html('Subscriptions');
  $('#content').html('');
  var userid = $('#u-id').val();
  $.ajax({
    type : 'POST',
    url : '/project/php/govenor.php',
    data : {action : 'get_posts', json : {action : 'subs', user_id : userid}},
    success : function(json){
      console.log(json);
      var posts = $.parseJSON(json);
      $.each(posts, function(key, val){
        $('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], false, val['POST_ID']));
      })
      $('.rate').click(function(){
        var postid = $( this ).attr('post-id');
        rate(postid);
        var old = $( this ).val();
        $( this ).val(old+1);
      })
      $('.otherprof').click(function(){
        var userid = $(this).attr('data-id');
        var name = $(this).text();
        getprofileposts(userid);
        var ownerid = $('#u-id').val();
        if(userid === ownerid)
          $('#content_head').html("My Profile");
        else
          $('#content_head').html(name+"'s Profile <a href='#' class='pull-right unsubscribe btn btn-warning' data-id='"+userid+"'>UnSubscribe!</a> <a href='#' class='pull-right subscribe btn btn-success' data-id='"+userid+"'>Subscribe!</a>");
        $('.subscribe').click(function(){
          var userid = $('#u-id').val();
          var otherid = $(this).attr('data-id');
          $.ajax({
              type : 'post',
              url : '/project/php/govenor.php',
              data : {action : 'subscribe', json : {suber : userid, subee : otherid}},
              success : function(hi){
                console.log(hi);
                window.location.replace('http://104.131.97.153/project/index.php');
              }
          })
        })

        $('.unsubscribe').click(function(){
            var userid = $('#u-id').val();
            var otherid = $(this).attr('data-id');
            $.ajax({
                type : 'post',
                url : '/project/php/govenor.php',
                data : {action : 'unsubscribe', json : {suber : userid, subee : otherid}},
                success : function(hi){
                  console.log(hi);
                  window.location.replace('http://104.131.97.153/project/index.php');
                }
            })
        })
      })
    }
  })
})


$('#profile').click(function(){
  var userid = $('#u-id').val();
  getprofileposts(userid);
})


$('#global').click(function(){
  $('#content_head').html('Global Feed');
  $('#content').html('');
  $.ajax({
    type : 'POST',
    url : '/project/php/govenor.php',
    data : {action : 'get_posts', json : {action : 'recent'}},
    success : function(json){
      //console.log(json);
      var posts = $.parseJSON(json);
      $.each(posts, function(key, val){
        $('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], false, val['POST_ID']));
      })
      $('.rate').click(function(){
        var postid = $( this ).attr('post-id');
        rate(postid);
        var old = $( this ).val();
        $( this ).val(old+1);
      })
      $('.otherprof').click(function(){
        var userid = $(this).attr('data-id');
        var name = $(this).text();
        getprofileposts(userid);
        var ownerid = $('#u-id').val();
        if(userid === ownerid)
          $('#content_head').html("My Profile");
        else
          $('#content_head').html(name+"'s Profile <a href='#' class='pull-right unsubscribe btn btn-warning' data-id='"+userid+"'>UnSubscribe!</a> <a href='#' class='pull-right subscribe btn btn-success' data-id='"+userid+"'>Subscribe!</a>");
        $('.subscribe').click(function(){
          var userid = $('#u-id').val();
          var otherid = $(this).attr('data-id');
          $.ajax({
              type : 'post',
              url : '/project/php/govenor.php',
              data : {action : 'subscribe', json : {suber : userid, subee : otherid}},
              success : function(hi){
                console.log(hi);
                window.location.replace('http://104.131.97.153/project/index.php');
              }
          })
        })

        $('.unsubscribe').click(function(){
            var userid = $('#u-id').val();
            var otherid = $(this).attr('data-id');
            $.ajax({
                type : 'post',
                url : '/project/php/govenor.php',
                data : {action : 'unsubscribe', json : {suber : userid, subee : otherid}},
                success : function(hi){
                  console.log(hi);
                  window.location.replace('http://104.131.97.153/project/index.php');
                }
            })
        })
      })
    }
  })
})

function rate(id) {
  $.ajax({
      type : 'POST',
      url : '/project/php/govenor.php',
      data : {action : 'rate', json : {action : 'rate', post_id : id}},
      success : function(woo){
        console.log(woo)
      }
    })
}

$( document ).ready(function(){
  var userid = $('#u-id').val();
  $.ajax({
    type : 'post',
    url : '/project/php/govenor.php',
    data : {action : "get_subscriptions", json : {user_id : userid}},
    success : function(subs){
      var all = $.parseJSON(subs);
      $.each(all, function(key, val){
        $('#bullet').append('<li><a href="#" class="otherprof" data-id="'+val['SUBEE']+'">'+val['uname']+'</a></li>');
      })
      $('.otherprof').click(function(){
        var userid = $(this).attr('data-id');
        var name = $(this).text();
        getprofileposts(userid);
        var ownerid = $('#u-id').val();
        if(userid === ownerid)
          $('#content_head').html("My Profile");
        else
          $('#content_head').html(name+"'s Profile <a href='#' class='pull-right unsubscribe btn btn-warning' data-id='"+userid+"'>UnSubscribe!</a> <a href='#' class='pull-right subscribe btn btn-success' data-id='"+userid+"'>Subscribe!</a>");
        $('.subscribe').click(function(){
          var userid = $('#u-id').val();
          var otherid = $(this).attr('data-id');
          $.ajax({
              type : 'post',
              url : '/project/php/govenor.php',
              data : {action : 'subscribe', json : {suber : userid, subee : otherid}},
              success : function(hi){
                console.log(hi);
                window.location.replace('http://104.131.97.153/project/index.php');
              }
          })
        })

        $('.unsubscribe').click(function(){
            var userid = $('#u-id').val();
            var otherid = $(this).attr('data-id');
            $.ajax({
                type : 'post',
                url : '/project/php/govenor.php',
                data : {action : 'unsubscribe', json : {suber : userid, subee : otherid}},
                success : function(hi){
                  console.log(hi);
                  window.location.replace('http://104.131.97.153/project/index.php');
                }
            })
        })
      })
    }
  })
  $('#content_head').html('Global Feed');
  $('#content').html('');
  $.ajax({
    type : 'POST',
    url : '/project/php/govenor.php',
    data : {action : 'get_posts', json : {action : 'recent'}},
    success : function(json){
      //console.log(json);
      var posts = $.parseJSON(json);
      $.each(posts, function(key, val){
        $('#content').append(makepost(val['TITLE'], val['CONTENT'], val['USERNAME'], val['USER_ID'], val['RATING'], false, val['POST_ID']));
      })
      $('.rate').click(function(){
        var postid = $( this ).attr('post-id');
        rate(postid);
        var old = $( this ).text();
        $( this ).text(old+1);
        console.log(old);
      })
      $('.otherprof').click(function(){
        var userid = $(this).attr('data-id');
        var name = $(this).text();
        getprofileposts(userid);
        var ownerid = $('#u-id').val();
        if(userid === ownerid)
          $('#content_head').html("My Profile");
        else
          $('#content_head').html(name+"'s Profile <a href='#' class='pull-right unsubscribe btn btn-warning' data-id='"+userid+"'>UnSubscribe!</a> <a href='#' class='pull-right subscribe btn btn-success' data-id='"+userid+"'>Subscribe!</a>");
        $('.subscribe').click(function(){
          var userid = $('#u-id').val();
          var otherid = $(this).attr('data-id');
          $.ajax({
              type : 'post',
              url : '/project/php/govenor.php',
              data : {action : 'subscribe', json : {suber : userid, subee : otherid}},
              success : function(hi){
                console.log(hi);
                window.location.replace('http://104.131.97.153/project/index.php');
              }
          })
        })

        $('.unsubscribe').click(function(){
            var userid = $('#u-id').val();
            var otherid = $(this).attr('data-id');
            $.ajax({
                type : 'post',
                url : '/project/php/govenor.php',
                data : {action : 'unsubscribe', json : {suber : userid, subee : otherid}},
                success : function(hi){
                  console.log(hi);
                  window.location.replace('http://104.131.97.153/project/index.php');
                }
            })
        })
      })
    }
  })
})