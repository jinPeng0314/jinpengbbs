<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Transformers\TopicTransformer;
use App\Http\Requests\Api\TopicRequest;

class TopicsController extends Controller
{
    public function index(Request $request,Topic $topic)
    {
        $query  = $topic->query(); //开始查询模型。

        //分类条件查询
        if ($categoryId = $request->category_id){
            $query->where('category_id', $categoryId);
        }

        // 为了说明 N+1问题，不使用 scopeWithOrder
        switch ($request->order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }

        $topics = $query->paginate(20);

        return $this->response->paginator($topics, new TopicTransformer());
    }

    public function store(TopicRequest $request,Topic $topic)
    {
        //用一组属性填充模型
        $topic->fill($request->all());
        $topic->user_id = $this->user()->id;
        $topic->save();

        return $this->response->item($topic,new TopicTransformer())->setStatusCode(201);
    }

    public function update(TopicRequest $request,Topic $topic)
    {
        $this->authorize('update',$topic); //为当前用户授予给定的操作
        $topic->update($request->all());

        return $this->response->item($topic,new TopicTransformer());
    }

    public function destroy(Topic $topic)
    {
        $this->authorize('update',$topic);
        $topic->delete();

        return $this->response->noContent();
    }

    public function userIndex(Request $request,User $user)
    {
        $topics = $user->topics()->recent()->paginate(20);

        return $this->response->paginator($topics,new TopicTransformer());
    }
}
