<?php /* Smarty version 2.6.19, created on 2015-11-15 11:20:51
         compiled from index.html */ ?>
<h1>Home Page </h1>
<div>
<p>Available News:</p>
<div id="news"></div>
</div>


<div style="border:2px solid silver;width:300px;padding:10px;margin:10px;float:left">
    <p>Post a news </p>
    <p>Title: <input type="text" name="title"/> </p>
    <p>Content: <input type="text" name="content"/> </p>
    <button id="postNews">Post news</button>
    <p id="postresult"></p>
</div>


<div style="border:2px solid silver;width:300px;padding:10px;margin:10px;float:left">
    <p>Edit a news </p>
    <p>Title: <input type="text" name="titleE"/> </p>
    <p>Content: <input type="text" name="contentE"/> </p>
    <p>Id <input type="text" name="idE"/> </p>
    <button id="editNews">Edit news</button>
    <p id="editresult"></p>
</div>

<div style="border:2px solid silver;width:300px;padding:10px;margin:10px;float:left">
    <p>Delete a news </p>
    <p>Id <input type="text" name="idD"/> </p>
    <button id="deleteNews">Delete news</button>
    <p id="deleteresult"></p>
</div>

<?php echo '
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" >
    $(document).ready(function(){
        getNews(0);
        $(document).on(\'click\', \'#postNews\', function(){
            postNews();
            
        })
        
        $(document).on(\'click\', \'#editNews\', function(){
            editNews();
         })
         
         $(document).on(\'click\', \'#deleteNews\', function(){
            deleteNews();
          })
        
        function getNews(id){
            $.ajax(\'app.php/news/\'+id,
                 {
                 type:\'get\',
                 dataType:\'json\',
                 data: {
                     
                 },
                 complete:function(data){
                        var response = JSON.parse(data.responseText);
                        if (response.result.length === 0)
                            return;
                        var html = \'<table border="1" style="width:500px;"><tr><td>id</td><td>title</td><td>text</td><td>date</td>\';
                        for(i in response.result){
                            html += \'<tr>\';
                            html += \'<td>\'+response.result[i].id +\'</td>\';
                            html += \'<td>\'+response.result[i].title +\'</td>\';
                            html += \'<td>\'+response.result[i].text +\'</td>\';
                            html += \'<td>\'+response.result[i].date +\'</td>\';
                            html += \'</tr>\';
                        }
                        html += \'</table>\';
                        $(\'#news\').html(html);
                        }
                 })  
        }
        
        function postNews(){
            var title = $(\'input[name="title"]\').val();
            var content = $(\'input[name="content"]\').val();
            
            $.ajax(\'app.php/news/\',
                 {
                 type:\'post\',
                 dataType:\'json\',
                 data: {
                     title:title,
                     content:content,
                 },
                 complete:function(data){
                        var response = JSON.parse(data.responseText);
                        $(\'#postresult\').html(\'<p>ID:\'+response.result.id+\'</p><p>Title:\'+response.result.title+\'</p><p>Content:\'+response.result.content+\'</p><p>Date:\'+response.result.date+\'</p>\');
                        getNews(0);
                    }
                 })  
        }
        
        function editNews(){
            
            var title = $(\'input[name="titleE"]\').val();
            var content = $(\'input[name="contentE"]\').val();
            var id = $(\'input[name="idE"]\').val();
            
            $.ajax(\'app.php/news/\'+id,
                 {
                 type:\'post\',
                 dataType:\'json\',
                 data: {
                     title:title,
                     content:content,
                 },
                 complete:function(data){
                        var response = JSON.parse(data.responseText);
                        $(\'#editresult\').html(\'<p>ID:\'+response.result.id+\'</p><p>Title:\'+response.result.title+\'</p><p>Content:\'+response.result.content+\'</p><p>Date:\'+response.result.date+\'</p>\');
                        if(typeof response.result.error != \'undefined\')
                            $(\'#editresult\').append(\'<p style="color:red;">ID:\'+response.result.error+\'</p>\');
                        getNews(0);
                    }
                 })  
        }
        
         function deleteNews(){
            
            var id = $(\'input[name="idD"]\').val();
            
            $.ajax(\'app.php/news/\'+id,
                 {
                 type:\'delete\',
                 dataType:\'json\',
                 complete:function(data){
                        var response = JSON.parse(data.responseText);
                        $(\'#deleteresult\').html(\'<p>ID:\'+response.result.id+\'</p>\');
                        if(typeof response.result.error != \'undefined\')
                            $(\'#deleteresult\').append(\'<p style="color:red;">ID:\'+response.result.error+\'</p>\');
                        getNews(0);
                    }
                 })  
        }
    })
</script>
'; ?>
