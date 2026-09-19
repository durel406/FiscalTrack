<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $table = 'app_notifications';

    protected $fillable = [
        'obligation_id',
        'user_id',
        'type',
        'title',
        'message',
        'dedupe_key',
        'read_at',
    ];

    protected $dates = ['read_at'];

    public function obligation()
    {
        return $this->belongsTo(Obligation::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function toFront()
    {
        $tone = $this->type === 'retard' ? 'late' : 'warn';

        return [
            'id'            => $this->id,
            'key'           => $this->dedupe_key,
            'obligation_id' => $this->obligation_id,
            'tone'          => $tone,
            'type'          => $this->type,
            'title'         => $this->title,
            'text'          => $this->message,
            'when'          => $this->created_at ? $this->created_at->format('d/m/Y H:i') : '—',
            'unread'        => is_null($this->read_at),
        ];
    }
}
