<?php

namespace App\Article\Controllers;

use App\Article\Models\Article;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ArticleController
{
    public function viewUserArticleListPage(Request $request)
    {
        try {
            $perPage = 12;

            $articles = Article::where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $articles->getCollection()
                ->transform(fn($article) => $article->jsonResponse());


            return view('pages.user.core.article_list', [
                'articles' => $articles,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewUserArticleSearchListPage(Request $request)
    {
        try {
            $perPage = 12;
            $search  = $request->get('q', null);

            $query = Article::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }


            $articles = $query->paginate($perPage);
            $articles->appends($request->query());

            $articles->getCollection()
                ->transform(fn($article) => $article->jsonResponse());



            return view('pages.user.core.article_search_list', [
                'articles' => $articles,
                'query' => $search,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }


    public function viewUserArticleDetailPage(Request $request, string $slug)
    {
        try {
            $article = Article::where('slug', $slug)
                ->where('status', 'published')
                ->firstOrFail();

            $article->increment('view_count');

            $site_logo = getSiteLogoURL();

            $socialShareLinks = generateSocialLinks([
                'url' => route('articles.slug.get', ['slug' => $slug]),
                'title' => $article->title,
                'description' => $article->description,
                'image' => $article->image ? getDownloadableLink($article->image)  : $site_logo,
            ]);

            return view('pages.user.core.article_detail', [
                'article' => $article->jsonResponse(),
                'socialShareLinks' => $socialShareLinks,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminArticleListPage(Request $request)
    {
        try {
            $sortBy  = $request->get('sortBy', 'last_updated');
            $orderBy = $request->get('orderBy', 'desc');
            $perPage = $request->get('perPage', 20);
            $search  = $request->get('query', null);

            $query = Article::query();

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            switch ($sortBy) {
                case 'last_updated':
                    $query->orderBy('updated_at', $orderBy)
                        ->orderBy('id', $orderBy);
                    break;

                case 'last_created':
                    $query->orderBy('created_at', $orderBy)->orderBy('id', $orderBy);
                    break;

                default:
                    $query->orderBy('updated_at', 'desc')
                        ->orderBy('id', 'desc');
            }

            $articles = $query->paginate($perPage);
            $articles->appends($request->query());

            $articles->getCollection()
                ->transform(fn($article) => $article->jsonResponse());

            return view('pages.admin.dashboard.article.article_list', [
                'articles' => $articles,
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminCreateArticlePage(Request $request)
    {
        try {
            return view('pages.admin.dashboard.article.edit_article', []);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function viewAdminEditArticlePage(Request $request, string $id)
    {
        try {
            $article = Article::findOrFail($id);

            return view('pages.admin.dashboard.article.edit_article', [
                'edit_article' => $article->jsonResponse(),
            ]);
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function createArticle(Request $request)
    {
        try {
            $validated = $request->validate([
                'title'        => 'required|string|max:255',
                'description'  => 'nullable|string',
                'content'      => 'required|string',
                'tags'         => 'nullable|string',
                'image'        => 'nullable|image|max:2048',
                'status'       => 'required|in:draft,published,archived',
                'is_featured'  => 'nullable|boolean',
                'published_at' => 'nullable|date',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = Storage::disk('public')
                    ->putFile('articles/thumbnails', $request->file('image'));
            }

            $validated['image'] = $imagePath;

            $validated['tags'] = $validated['tags'] ? explode(',', $validated['tags']) : null;

            Article::create($validated);

            return redirect()->back()
                ->with('success', 'Article created successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function updateArticle(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);

            $validated = $request->validate([
                'title'        => 'nullable|string|max:255',
                'description'  => 'nullable|string',
                'content'      => 'nullable|string',
                'tags'         => 'nullable|string',
                'image'        => 'nullable|image|max:2048',
                'status'       => 'nullable|in:draft,published,archived',
                'is_featured'  => 'nullable|boolean',
                'published_at' => 'nullable|date',
                'remove_image' => 'nullable|boolean',
            ]);

            if ($request->has('remove_image') && $request->boolean('remove_image')) {
                if ($article->image) {
                    Storage::disk('public')->delete($article->image);
                }
                $article->image = null;
            }

            if ($request->hasFile('image')) {
                if ($article->image && Storage::disk('public')->exists($article->image)) {
                    Storage::disk('public')->delete($article->image);
                }

                $article->image = Storage::disk('public')
                    ->putFile('articles/thumbnails', $request->file('image'));
            }


            $article->fill([
                "title" => $validated['title'],
                "description" => $validated['description'],
                'tags' => $validated['tags'] ? explode(',', $validated['tags']) : null,
                'content' => $validated['content'],
                'status' => $validated['status'],
                'is_featured' => $validated['is_featured']
            ]);
            $article->save();

            return redirect()->back()
                ->with('success', 'Article updated successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteArticle($id)
    {
        try {
            $article = Article::findOrFail($id);

            if ($article->image) {
                Storage::disk('public')->delete($article->image);
            }

            $article->delete();

            return redirect()->back()
                ->with('success', 'Article deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteSelectedArticles(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()
                    ->with('error', 'No articles selected for deletion.');
            }

            $articles = Article::whereIn('id', $ids)->get();

            foreach ($articles as $article) {
                if ($article->image) {
                    Storage::disk('public')->delete($article->image);
                }
                $article->delete();
            }

            return redirect()->back()
                ->with('success', 'Selected articles deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }

    public function deleteAllArticles()
    {
        try {
            $articles = Article::all();

            foreach ($articles as $article) {
                if ($article->image) {
                    Storage::disk('public')->delete($article->image);
                }
                $article->delete();
            }

            return redirect()->back()
                ->with('success', 'All articles deleted successfully.');
        } catch (Exception $e) {
            return handleErrors($e);
        }
    }
}
