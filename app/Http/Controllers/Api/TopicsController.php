<?php

namespace App\Http\Controllers\Api;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Transformers\TopicTransformer;
use App\Http\Requests\Api\TopicRequest;

class TopicsController extends Controller
{
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
}
