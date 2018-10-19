<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use \XMLReader;
use \DOMDocument;

class Importcontroller extends Controller{
      protected function data()
      {
          $doc = new DOMDocument;
          $xml = new XMLReader;
          $mainArray = [];
          $tags = [];
          $postid = 0;
          $xml->open('/Users/leanne/Sites/php-script/wordpress.xml');  
        
          /**
           * stole this code from : 
           */
          while ($xml->read() && $xml->name !== 'item');
          
          while ($xml->name === 'item')
          {
              $node = simplexml_import_dom($doc->importNode($xml->expand(), true));
              
              if ($xml->nodeType == XMLReader::ELEMENT && !empty($node->category)) {
                    //$tags[] = $xml->getAttribute('nicename');
              }
          
              $mainArray[] = ['postid'=>$node->postid, 'title'=>$node->title, 'published_at'=>$node->wppostdategmt, 'content'=>(string) $node->contentencoded, 'slug'=>$node->wppostname];
              //$tags = [];
              $xml->next('item');
          }
      
      
          $tags = [];
          $xml = new XMLReader();
      
          if (!$xml->open('/Users/leanne/Sites/php-script/wordpress.xml')) 
          {
              die("Failed to open 'data.xml'");
          }
      
          while($xml->read()) 
          {
              if ($xml->nodeType == XMLReader::ELEMENT && $xml->localName == 'postid') 
              {
                $node = simplexml_import_dom($doc->importNode($xml->expand(), true));
                $postid = $node;
              }
              if ($xml->nodeType == XMLReader::ELEMENT && $xml->name == 'category') 
              {
                  $tags[$postid][] = $xml->getAttribute('nicename');
              }
              
          }
      
          $newArray = array();
          foreach($mainArray as $post)
          {
              $id = $post['postid'];
              if(!empty($tags["$id"])){
                $post['tags'] = $tags["$id"];
                $newArray[] = $post;
              }
          }
      
          return $newArray;
      }

      /**
       * selecting tags taht contain ids from post request
       * extrac
       */
      protected function example(array $mainArray = [], array $post = [])
      {
        $newArray = array();
        foreach($mainArray as $post)
        {
            $id = $post['postid'];
            if(!empty($tags["$id"])){
              $post['tags'] = $tags["$id"];
              $newArray[] = $post;
            }
        }
    
        return $newArray;
      }

      protected function handleTag(string $tag)
      {
        $instance = Tag::where('name', trim($tag))->first();
        if(null === $instance){
            return Tag::create(['name' => $tag]);
        }
        return $instance;
        $tag = Tag::where("postid",656)->first();
        dd($tag);
      }

      public function import()
      {
          $allBlogs = $this->data();

          foreach($allBlogs as $singleBlog)
          {
            $id = $singleBlog['postid'];
 
            // $blog = Blog::where("postid",12)->first();
            // dd($blog);
           $b = Blog::updateOrCreate(
                ['postid' => $id], 
            [
                'title'=>$singleBlog['title'],
                'content'=>$singleBlog['content'],
                'slug'=>$singleBlog['slug'],
                'published_at' =>$singleBlog['published_at'],
                'client_id' => 1, 
                'status_id' => 1, 
                'site_id'=> 2, 
                'author_id' => 1, 
                
            ]);
            
            // $blog = Blog::where("postid",656)->first();
            // // dd($blog);
            // // $blog = Blog::find(1);
            // $tags = $blog->tags()->get();
            // dump($tags['name']);
            // foreach($tags as $tag){
            //     dump($tag['name']);
            // }


            // $b = new Blog();
            // $b->fill($singleBlog);
            // $b->client_id = 1;
            // $b->status_id = 1;
            // $b->site_id = 2;
            // $b->author_id = 1;
            // $b->save(); 

            // $id = $singleBlog['postid'];

            if(!empty($id))
            {
                $tagIds = array();
              foreach($singleBlog["tags"] as $singleTag)
              {
                $tag = $this->handleTag($singleTag);
                array_push($tagIds, $tag->id);
              // $singleTag = Tag::find($id)->tags()->save($tags);
              }
            //   $b->tags()->attach($t->id);
              $b->tags()->sync($tagIds);

                // $tag = Tag::where("postid",12)->first();
                // dd($tag);              

            }

        
            
            //$b has the primary key
          
          // foreach($allBlogs as $singleTag){
          //   $currentTag = $singleTag['tags'];
          //   // dd($currentTag);
            
          //   foreach($currentTag as $thisTag){
          //     $t = new Tag();
          //     $t->fill($thisTag);
          //     $t->site_id = 2;
          //     $t->save(); 
          //   }
            // dd($singleTag);
            // echo $singleTag['tags'];
            // $t = new Tag();
            // $t->fill($singleTag);
            // dd($singleTag);
            // $t->site_id = 2;
            // $t->save(); 

            //$b has the primary key
      


          // $saveResult = EloquentModel::create($insertArray);

          // $allBlogs = $this->data();
          // foreach($newArray as $singleTag){
          //   $t = new Tag();
          //   $t->fill($singleTag);
          //   $t->client_id = 1;
          //   $t->name;
          //   dd($singleTag);
          //   // $t->save(); 

            //$b has the primary key
          
        }

        // TO PRINT OUT A SPECIFIC BLOG POST DO THE BELOW: : 
            $blog = Blog::where("postid",4446)->first();
            dd($blog);
            $blog = Blog::find(1);

        // TO PRINT OUT THE TAGS IN AN ARRAY: I.E. SEE WHAT TAGS ARE THERE: 
            // $blog = Blog::where("postid",4446)->first();
            // $tags = $blog->tags()->get();
            // dump($tags);
            // foreach($tags as $tag){
            //     dump($tag['name']);
            // }
    }

}