<?php

namespace kalanis\UploadPerPartes\examples\controller;


use Exception;
use kalanis\UploadPerPartes;
use Lib;
use OpenApi\Annotations as OA;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class SymfonyUploader
 * @package kalanis\UploadPerPartes\examples\controller
 * What can be displayed on page for content upload
 */
class SymfonyUploader extends AbstractController //  extends \yourFavouriteFrameworkControllerClass
{
    const TARGET_UPLOAD_PATH = '/path-to-upload-target-dir/';

    /** @var UploadPerPartes\examples\Uploader */
    protected UploadPerPartes\examples\Uploader $uploader;

    public function __construct(ContainerInterface $container)
    {
        $this->uploader = new UploadPerPartes\examples\Uploader(
            $container,
            [
            'temp_location' => '/temp/',
            'final_storage' => new \kalanis\UploadPerPartes\examples\FinalDestination(
                new Lib\Content\FileSave($this->getUser()), $this->getSession()
            ),
            'calc_size' => 1048576, // segment size: 1024*1024*1 = 1M
        ]); // here temp path and init everytime with it
    }

    /**
     * @OA\Post(
     *     path="/upload/v1/init",
     *     summary="Initialize upload",
     *     tags={"Uploading"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="fileName", type="string", description="Uploaded file name"),
     *                 @OA\Property(property="fileSize", type="integer", description="Total size of file"),
     *                 @OA\Property(property="clientData", type="string", description="Roundabout info package")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Initialized session",
     *         @OA\JsonContent(
     *             @OA\Property(property="serverKey", type="string", description="Shared key"),
     *             @OA\Property(property="status", type="string", description="How the upload runs"),
     *             @OA\Property(property="errorMessage", type="string", description="When became problems, the description will be here")
     *             @OA\Property(property="roundaboutClient", type="string", description="Roundabout info package from server"),
     *             @OA\Property(property="name", type="string", description="Uploaded file name - from server"),
     *             @OA\Property(property="totalParts", type="integer", description="How many parts will be wanted")
     *             @OA\Property(property="lastKnownPart", type="integer", description="Last known part on server - from previous try")
     *             @OA\Property(property="partSize", type="integer", description="How big will be datain single part before encoding")
     *             @OA\Property(property="encoder", type="string", description="Which encoder will be used to pack data"),
     *             @OA\Property(property="check", type="string", description="Which method will be used to calculate checksum")
     *         )
     *     ),
     * )
     * @param Request $request
     * @throws Exception
     * @return JsonResponse
     */
    public function init(Request $request): JsonResponse
    {
        return new JsonResponse($this->uploader->init(
            static::TARGET_UPLOAD_PATH,
            strval($request->request->get('fileName')),
            intval(strval($request->request->get('fileSize'))),
            strval($request->request->get('clientData'))
        ));
    }

    /**
     * @OA\Post(
     *     path="/upload/v1/check",
     *     summary="Check parts of upload",
     *     tags={"Uploading"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="serverData", type="string", description="Shared key"),
     *                 @OA\Property(property="segment", type="integer", description="Which segment will be calculated"),
     *                 @OA\Property(property="method", type="string", description="How the segment checksum will be calculated"),
     *                 @OA\Property(property="clientData", type="string", description="Roundabout info package")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Checksum for part",
     *         @OA\JsonContent(
     *             @OA\Property(property="serverKey", type="string", description="Shared key"),
     *             @OA\Property(property="status", type="string", description="How the upload runs"),
     *             @OA\Property(property="errorMessage", type="string", description="When became problems, the description will be here")
     *             @OA\Property(property="roundaboutClient", type="string", description="Roundabout info package from server"),
     *             @OA\Property(property="method", type="string", description="How the segment checksum was calculated"),
     *             @OA\Property(property="checksum", type="string", description="Checksum of part"),
     *         )
     *     ),
     * )
     * @param Request $request
     * @throws Exception
     * @return JsonResponse
     */
    public function check(Request $request): JsonResponse
    {
        return new JsonResponse($this->uploader->check(
            strval($request->request->get('serverData')),
            intval(strval($request->request->get('segment'))),
            strval($request->request->get('method')),
            strval($request->request->get('clientData'))
        ));
    }

    /**
     * @OA\Post(
     *     path="/upload/v1/check",
     *     summary="Truncate upload after part",
     *     tags={"Uploading"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="serverData", type="string", description="Shared key"),
     *                 @OA\Property(property="segment", type="integer", description="Which segment will be last"),
     *                 @OA\Property(property="clientData", type="string", description="Roundabout info package")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Checksum for part",
     *         @OA\JsonContent(
     *             @OA\Property(property="serverKey", type="string", description="Shared key"),
     *             @OA\Property(property="status", type="string", description="How the upload runs"),
     *             @OA\Property(property="errorMessage", type="string", description="When became problems, the description will be here")
     *             @OA\Property(property="roundaboutClient", type="string", description="Roundabout info package from server"),
     *             @OA\Property(property="lastKnownPart", type="integer", description="Last known part on server")
     *         )
     *     ),
     * )
     * @param Request $request
     * @throws Exception
     * @return JsonResponse
     */
    public function truncate(Request $request): JsonResponse
    {
        return new JsonResponse($this->uploader->truncateFrom(
            strval($request->request->get('serverData')),
            intval(strval($request->request->get('segment'))),
            strval($request->request->get('clientData'))
        ));
    }

    /**
     * @OA\Post(
     *     path="/upload/v1/part",
     *     summary="Sent part of data from file",
     *     tags={"Uploading"},
     *     @OA\RequestBody(
     *         @OA\MediaType(mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="serverData", type="string", description="Shared key"),
     *                 @OA\Property(property="content", type="string", format="byte", description="The data itself"),
     *                 @OA\Property(property="method", type="string", description="How the segment has been encoded"),
     *                 @OA\Property(property="clientData", type="string", description="Roundabout info package")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Status if upload is correct",
     *         @OA\JsonContent(
     *             @OA\Property(property="serverKey", type="string", description="Shared key"),
     *             @OA\Property(property="status", type="string", description="How the upload runs"),
     *             @OA\Property(property="errorMessage", type="string", description="When became problems, the description will be here")
     *             @OA\Property(property="roundaboutClient", type="string", description="Roundabout info package from server"),
     *             @OA\Property(property="lastKnownPart", type="integer", description="Last known part on server - from previous step")
     *         )
     *     ),
     * )
     * @param Request $request
     * @throws Exception
     * @return JsonResponse
     */
    public function part(Request $request): JsonResponse
    {
        return new JsonResponse($this->uploader->upload(
            strval($request->request->get('serverData')),
            strval($request->request->get('content')),
            strval($request->request->get('method')),
            strval($request->request->get('clientData'))
        ));
    }

    /**
     * @OA\Post(
     *     path="/upload/v1/cancel",
     *     summary="Cancel the whole upload",
     *     tags={"Uploading"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="serverData", type="string", description="Shared key"),
     *                 @OA\Property(property="clientData", type="string", description="Roundabout info package")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Status about upload shutdown",
     *         @OA\JsonContent(
     *             @OA\Property(property="serverKey", type="string", description="Shared key"),
     *             @OA\Property(property="status", type="string", description="How the upload runs"),
     *             @OA\Property(property="errorMessage", type="string", description="When became problems, the description will be here")
     *             @OA\Property(property="roundaboutClient", type="string", description="Roundabout info package from server"),
     *         )
     *     ),
     * )
     * @param Request $request
     * @throws Exception
     * @return JsonResponse
     */
    public function cancel(Request $request): JsonResponse
    {
        return new JsonResponse($this->uploader->cancel(
            strval($request->request->get('serverData')),
            strval($request->request->get('clientData'))
        ));
    }

    /**
     * @OA\Post(
     *     path="/upload/v1/done",
     *     summary="Close the whole upload",
     *     tags={"Uploading"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 @OA\Property(property="serverData", type="string", description="Shared key"),
     *                 @OA\Property(property="clientData", type="string", description="Roundabout info package")
     *             ),
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Status about upload shutdown",
     *         @OA\JsonContent(
     *             @OA\Property(property="serverKey", type="string", description="Shared key"),
     *             @OA\Property(property="status", type="string", description="How the upload runs"),
     *             @OA\Property(property="errorMessage", type="string", description="When became problems, the description will be here")
     *             @OA\Property(property="roundaboutClient", type="string", description="Roundabout info package from server"),
     *             @OA\Property(property="name", type="string", description="Final name on server"),
     *         )
     *     ),
     * )
     * @param Request $request
     * @throws Exception
     * @return JsonResponse
     */
    public function done(Request $request): JsonResponse
    {
        return new JsonResponse($this->uploader->done(
            strval($request->request->get('serverData')),
            strval($request->request->get('clientData'))
        ));
    }
}
