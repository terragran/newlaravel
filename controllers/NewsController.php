<?php

use Carbon\Carbon;
use Lib\News\NewsRepository;
use Lib\Services\Scraping\Scraper;
use Lib\Services\Validation\NewsValidator;

class NewsController extends \BaseController {

	/**
	 * News repository instance.
	 * 
	 * @var Lib\News\NewsRepository
	 */
	protected $repo;

	/**
	 * validator instance.
	 * 
	 * @var Lib\Services\Validation\NewsCreateValidator
	 */
	private $validator;

	/**
	 * News scraper isntance.
	 * 
	 * @var Lib\Services\Scraping\NewScraper;
	 */
	private $scraper;

	public function __construct(NewsRepository $news, NewsValidator $validator, Scraper $scraper)
	{
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('news:delete', array('only' => 'destroy'));
		$this->beforeFilter('news:update', array('only' => 'updateFromExternal'));

		if (App::environment() === 'demo') {
			$this->beforeFilter('news:create', array('only' => array('store')));
			$this->beforeFilter('news:edit', array('only' => array('update')));
		} else {
			$this->beforeFilter('logged', array('except' => array('index', 'show', 'paginate')));
			$this->beforeFilter('news:create', array('only' => array('create', 'store')));
			$this->beforeFilter('news:edit', array('only' => array('edit', 'update')));
		}

		$this->repo = $news;
		$this->scraper = $scraper;
		$this->validator = $validator;
	}

	/**
	 * Display list of paginated news.
	 *
	 * @return View
	 */
	public function index()
	{
		$input = Input::all();
$urstate=last(explode('-', $_SERVER["REQUEST_URI"]));
	$urcityy=explode('/', $_SERVER["REQUEST_URI"]);
	$urcityyy=explode('-', ucwords(strtolower($urcityy[1])));
	$simplificada=last(explode('_', ucwords(strtolower($urcityy[1]))));
	$urcityyyyyy=explode('_', ucwords(strtolower($urcityy[1])));
	
	list($first)=explode('-', $urcityy[1]);
	list($firstalerta)=explode('?', $urcityy[1]);
	list($firstalerta2)=explode('?', $firstalerta);
	//list($_, $_,$_, $fourth)=explode('-', $urcityy[2]);
	//list($_, $_,$_,$_, $fifth)=explode('-', $urcityy[2]);
	/*
	list($_, $_,$_, $fourth)=explode('-', $urcityy[2]);
	list($_, $_,$_,$_, sizeof($fifth)-1)=explode('-', $urcityy[2]);
	list($_, $_,$_,$_,$_, $sixth)=explode('-', $urcityy[2]);
	list($_, $_,$_,$_,$_,$_, $seven)=explode('-', $urcityy[2]);
	
	$urcityyyy=ucwords(strtolower($third)).' '.ucwords(strtolower($fourth)).' '.ucwords(strtolower($fifth)).' '.ucwords(strtolower($sixth)).' '.ucwords(strtolower($seven));
			*/
			//$genre = (isset(ucwords(strtolower($urcityyy[4]))) ? ucwords(strtolower($urcityyy[4])) : null).
			
			
			//$aqui7 =isset($urcityyy[7]) ? $urcityyy[7] : null;
			$urcityyyy=	ucwords(strtolower((isset($urcityyy[2]) ? $urcityyy[2] : null).' '.(isset($urcityyy[3]) ? $urcityyy[3] : null).' '.(isset($urcityyy[4]) ? $urcityyy[4] : null).' '.(isset($urcityyy[5]) ? $urcityyy[5] : null).' '.(isset($urcityyy[6]) ? $urcityyy[6] : null).' '.(isset($urcityyy[7]) ? $urcityyy[7] : null)));
			$alerta=	ucwords(strtolower((isset($urcityyy[1]) ? $urcityyy[1] : null).' '.(isset($urcityyy[2]) ? $urcityyy[2] : null).' '.(isset($urcityyy[3]) ? $urcityyy[3] : null).' '.(isset($urcityyy[4]) ? $urcityyy[4] : null).' '.(isset($urcityyy[5]) ? $urcityyy[5] : null).' '.(isset($urcityyy[6]) ? $urcityyy[6] : null).' '.(isset($urcityyy[7]) ? $urcityyy[7] : null)));
	$urs=last(explode('_', $_SERVER["REQUEST_URI"]));
	$urslast=last(explode('/', $_SERVER["REQUEST_URI"]));
	$urst=explode('=', $_SERVER["REQUEST_URI"]);
	list($firsttoken)=explode('_',isset($urst[1]) ? $urst[1] : null);
	//dd($urcityyyy);
	//exit;
	if ($first=='empregos' and str::slug($urcityyyy)){
	   
	   $data = \Title::where( 'city', ucwords(strtolower(str_replace('-'," ", $urcityyyy))) )->where('approved',1)
					->orderby('created_at','desc')
					->paginate( 10 );
					//isset($urcityyy[2]) ? $urcityyy[2] : null)
					}elseif (isset($urcityyy[2]) ? $urcityyy[2] : null){
					 $data = \Title::where( 'city', ucwords(strtolower(str_replace('-'," ", $urcityyyy))) )->where( 'bairro','LIKE','%'.str::title(str_replace('-'," ", $urcityy[2])).'%' )->where( 'category','LIKE','%'.str::title(str_replace('-'," ", $urcityy[2])).'%' )->where('approved',1)
					->orderby('featured','1')->orderby('created_at','desc')
					->paginate( 10 );
	
					}else{
					$data = \Title::where('approved',1)
					->orderby('featured','1')->orderby('created_at','desc')
					->paginate( 10 );
					}
		return View::make('News.Index')->with('data',$data);
	}

	/**
	 * Display form for creating new news items.
	 *
	 * @return View
	 */
	public function create()
	{
		return View::make('News.Create');
	}

	/**
	 * Store a newly created news item.
	 *
	 * @return Redirect
	 */
	public function store()
	{
		$input = Input::except('_token');

		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}

		//escape double qoutes
		$input['title'] = htmlspecialchars($input['title']);
		
		$this->repo->store($input);

		return Redirect::back()->withSuccess( trans('main.news create success') );
	}

	/**
	 * Display single news items.
	 *
	 * @param  int  $id
	 * @return View
	 */
	public function show($id)
	{
		$news = $this->repo->byId($id);

		if (($news->full_url && ! $news->fully_scraped) && ! app('mtdb.currentRequestIsFromBot'))
		{
			$news = $this->repo->getFullNewsItem($news);
		}

		return View::make('News.Show')->with(compact('news'))->withRecent($this->repo->latest());
	}

	/**
	 * Displays form for editing news item.
	 *
	 * @param  int  $id
	 * @return View
	 */
	public function edit($id)
	{
		$news = $this->repo->byId($id);

		return View::make('News.Edit')->withNews($news);
	}

	/**
	 * Updates the news item.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function update($id)
	{
		$input = Input::except('_token', '_method');

		$news = $this->repo->byId($id);

		if ($news->title === $input['title'])
		{
			//dont check for title uniqueness when updating if
			//title was not updated.
			$this->validator->rules['title'] = 'required|min:2|max:255';
		}
		
		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}

		//escape double qoutes
		$input['title'] = htmlspecialchars($input['title']);

		$this->repo->update($news, $input);	

		return Redirect::back()->withSuccess( trans('main.news update success') );
	}

	/**
	 * Delete specified news item.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->repo->delete($id);		

		return Response::json(trans('main.news delete success'), 200);
	}

	/**
	 * Updates news from external sources.
	 * 
	 * @return void
	 */
	public function updateFromExternal()
	{
		$this->scraper->updateNews();

		Event::fire('News.Updated', Carbon::now());

		return Redirect::back()->withSuccess( trans('dash.updated news successfully') );
	}

}