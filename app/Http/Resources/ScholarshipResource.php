<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'link' => $this->link,
            'official_link' => $this->official_link,
            'post_at' => $this->post_at,
            'deadline' => $this->deadline,
            'eligibility' => $this->eligibility,
            'host_country' => $this->host_country,
            'host_university' => $this->host_university,
            'program_duration' => $this->program_duration,
            'degree_offered' => $this->degree_offered,
            'url' => url($this->slug),
            'api_url' => url("api/scholarship/{$this->slug}"),
            'urls' => [
                'web' => url($this->slug),
                'api' => url("api/scholarship/{$this->slug}"),
                'api_by_id' => url("api/scholarship/{$this->id}"),
            ]
        ];
    }
}
