<?php

namespace Tests\Feature\Models\Video;

use App\Models\Genre;
use App\Models\Video;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class VideoCrudTest extends BaseVideoTestCase
{

    use DatabaseMigrations;

    private $fileFieldsData = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (Video::$fileFields as $field) {
            $this->fileFieldsData[$field] = "$field.test";
        }
    }

    public function testList()
    {
        factory(Video::class,1 )->create();
        $videos = Video::all();
        $this->assertCount(1, $videos);

        $videosKeys = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'opened',
                'rating',
                'duration',
                'thumb_file',
                'banner_file',
                'trailer_file',
                'video_file',
                'deleted_at',
                'created_at',
                'updated_at'
            ],
            $videosKeys
        );
    }
    
    public function testCreateWithBasicFields()
    {
        $video = Video::create($this->data + $this->fileFieldsData);
        $video->refresh();

        $this->assertEquals(36, strlen($video->id));
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas(
            'videos', 
            $this->data + $this->fileFieldsData + ['opened' => false ]
        );

        $video = Video::create($this->data + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true ]);
        
    }


    public function testCreateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
        
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try{
            Video::create([
                'categories_id' => [0,1,2]
            ]);

        }catch(QueryException $exception){
            $this->assertCount(0,Video::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }


    public function testRollbackUpdate()
    {
        $video = factory(Video::class)->create();
        $oldTitle = $video->title;
        try{
            $video->update([
                'title' => 'title xx',
                'description' => 'description',
                'year_launched' => 2019,
                'rating' => Video::RATING_LIST[0],
                'duration' => 90,
                'categories_id' => [0,1,2]
            ]);
        }catch(QueryException $exception){
            $this->assertDatabaseHas('videos', ['title' => $oldTitle]);
            $hasError = true;
        }
    }

    public function testUpdateWithBasicFields()
    {
        $video = factory(Video::class)->create(
            ['opened' => false]
        );
        $video->update($this->data + $this->fileFieldsData);
        $this->assertFalse($video->opened);
        $this->assertDatabaseHas('videos', $this->data + $this->fileFieldsData + ['opened' => false]);
        
        $video = factory(Video::class)->create(
            ['opened' => false]
        );
        $video->update($this->data + $this->fileFieldsData + ['opened' => true]);
        $this->assertTrue($video->opened);
        $this->assertDatabaseHas('videos', $this->data + ['opened' => true]);
    }


    public function testUpdateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = factory(Video::class)->create();
        $video->update($this->data + [
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id],
        ]);

        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }


    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video',[
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video',[
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }

    public function testHandleRelations()
    {
        $video = factory(Video::class)->create();
        Video::handleRelations($video,[]);
        $this->assertCount(0, $video->categories);

        $category = factory(Category::class)->create();
        // dd($category, $video->categories);
        Video::handleRelations($video,[
            'categories_id' => [$category->id]
        ]);
        $video->refresh();
        $this->assertCount(1,$video->categories);

        $genre = factory(Genre::class)->create();
        Video::handleRelations($video,[
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1,$video->genres);

        $video->categories()->delete();
        $video->genres()->delete();

        Video::handleRelations($video,[
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ]);
        $video->refresh();
        $this->assertCount(1,$video->categories);
        $this->assertCount(1,$video->genres);
    }


    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class,3)->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[0]]
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'categories_id' => [$categoriesId[1], $categoriesId[2]]
        ]);
        $this->assertDatabaseMissing('category_video', [
            'category_id' => $categoriesId[0],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoriesId[2],
            'video_id' => $video->id
        ]);
    }


    public function testSyncGenres()
    {
        $genresId = factory(Genre::class,3)->create()->pluck('id')->toArray();
        $video = factory(Video::class)->create();
        Video::handleRelations($video, [
            'genres_id' => [$genresId[0]]
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);

        Video::handleRelations($video, [
            'genres_id' => [$genresId[1], $genresId[2]]
        ]);
        $this->assertDatabaseMissing('genre_video', [
            'genre_id' => $genresId[0],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[1],
            'video_id' => $video->id
        ]);
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genresId[2],
            'video_id' => $video->id
        ]);
    }


    public function testDelete()
    {
        $video = factory(Video::class)->create();
        $video->delete();
        $this->assertNull(Video::find($video->id));

        $video->restore();
        $this->assertNotNull(Video::find($video->id));
    }

}