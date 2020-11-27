<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookShelfResource;
use App\Models\Article;
use App\Models\Book;
use App\Models\BookShelf;
use HungCP\PhpSimpleHtmlDom\HtmlDomParser;
use Illuminate\Http\Request;

class BookController extends Controller
{
    //

    public function getBookShelf()
    {
        $books = BookShelf::query()->with('book')->get();
        return BookShelfResource::collection($books);
    }

    public function index()
    {
        $books = Book::query()->paginate();
        return BookResource::collection($books);
    }

    public function getBook(Request $request)
    {
        $book = Book::query()->where('book_number', $request->bid)->get();
        if ($book->isEmpty()) {
            $url = "http://www.xbiquge.la/01/{$request->bid}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            $html = curl_exec($ch);
            curl_close($ch);
            $new = str_replace(array("\r","\n","/t","/s"),"",$html);
            preg_match("/<h1>.*?<\/h1>/ism",$new,$title);
            preg_match("/<p *?>.*?<\/p>/ism",$new,$match);
            preg_match("/<div id=\"fmimg\".*?>.*?<\/div>/ism",$new,$img);
            $html = \HungCP\PhpSimpleHtmlDom\HtmlDomParser::str_get_html($title[0]);
            $img = HtmlDomParser::str_get_html($img[0]);
            $data = [
                'book_number' => $request->bid,
                'book_name' => $html->find('h1')[0]->plaintext,
                'book_author' => strstr(substr($match[0],36),'</p>', true),
                'book_img' => $img->find('img')[0]->src
            ];
            Book::create($data);
            $book = Book::query()->where('book_number', $request->bid)->get();
        }
        return BookResource::collection($book);

    }

    public function getCatalog(Request $request)
    {
        $url = "http://www.xbiquge.la/01/{$request->bid}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $html = curl_exec($ch);
        curl_close($ch);
        $new = str_replace(array("\r","\n","/t","/s"),"",$html);
        preg_match("/<div id=\"list\".*?>.*?<\/div>/ism",$new,$list);
        $dom = HtmlDomParser::str_get_html($list[0]);
        foreach ($dom->find('a') as $e) {
            $cataLog[] = [
                'url' => '/api/v1/books'.$e->href,
                'title' => $e->plaintext
            ];
        }

        return $cataLog;
    }
    public function getBookInfo(Request $request)
    {
        $num = strstr($request->num,'.html', true);
        $url = "http://www.xbiquge.la/$request->cid/$request->bid/$request->num";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        $html = curl_exec($ch);
        curl_close($ch);
        $new = str_replace(array("\r","\n","/t","/s"),"",$html);
        preg_match("/<h1>.*?<\/h1>/ism",$new,$title);
        echo $title[0] ?? '';
        preg_match("/<div id=\"content\".*?>.*?<\/div>/ism",$new,$match);
        preg_match("/<div class=\"bottem1\".*?>.*?<\/div>/ism",$new,$next);
        preg_match_all("/<a href=\"\/$request->cid\/$request->bid\/.*?\".*?>.*?<\/a>/ism",$next[0],$next);
        echo $match[0] ?? '';
//        $post = array_merge($title,$match);
        echo HtmlDomParser::str_get_html($next[0][1])->find('a')[0]->href .'下一章';

//
//        foreach ($post as $value) {
//            echo $value;
//        }
    }

    public function searchBook(Request $request)
    {
        $url  = "http://www.xbiquge.la/modules/article/waps.php";
        $data = [
            'searchkey' => $request->name
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        $new = str_replace(array("\r","\n","/t","/s"),"",$output);
        $html = \HungCP\PhpSimpleHtmlDom\HtmlDomParser::str_get_html($new);
//        foreach ($html->find('td') as $e) {
//            echo $e->find('a')[0]->href;
//            break;
//        }
        $url = $html->find('td')[0]->find('a')[0]->href;
        $url = strstr(strstr($url,'la/'),'/');
        $name = $html->find('td')[0]->find('a')[0]->plaintext;
        echo "<a href='/api/v1/catalog$url'>$name </a>";

    }
}
