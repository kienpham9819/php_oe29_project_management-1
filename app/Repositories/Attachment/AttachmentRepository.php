<?php

namespace App\Repositories\Attachment;

use App\Repositories\Attachment\AttachmentRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Attachment;

class AttachmentRepository extends BaseRepository implements AttachmentRepositoryInterface
{
    public function getModel()
    {
        return Attachment::class;
    }
}
