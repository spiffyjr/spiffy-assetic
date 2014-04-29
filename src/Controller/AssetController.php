<?php

namespace SpiffyAssetic\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Mvc\Controller\AbstractActionController;

class AssetController extends AbstractActionController
{
    public function indexAction()
    {
        $match = $this->getEvent()->getRouteMatch();
        $params = $match->getParams();
        $services = $this->getServiceLocator();

        /** @var \SpiffyAssetic\AsseticService $asseticService */
        $asseticService = $services->get('SpiffyAssetic\AsseticService');
        $asseticService->load();

        $am = $asseticService->getAssetManager();
        $name = $params['name'];
        $pos = $params['pos'];

        $asset = $am->get($name);
        if (null !== $pos && !$asset = $this->findAssetLeaf($asset, $pos)) {
            echo sprintf('The "%s" asset does not include a leaf at position %d.', $name, $pos);
            exit;
        }

        $response = new Response();
        $response->setExpires(new \DateTime());

        $lastModified = $am->getLastModified($asset);
        if (null !== $lastModified) {
            $date = new \DateTime();
            $date->setTimestamp($lastModified);
            $response->setLastModified($date);
        }

        if ($am->hasFormula($name)) {
            $formula = $am->getFormula($name);
            $formula['last_modified'] = $lastModified;
            $response->setETag(md5(serialize($formula)));
        }

        $request = $services->get('request');
        if ($response->isNotModified($request)) {
            return $response;
        }

        $extension = pathinfo($asset->getTargetPath(), PATHINFO_EXTENSION);
        $contentType = $request->getMimeType($extension);

        if (null !== $contentType) {
            $response->headers->set('Content-Type', $contentType);
        }

        $response->setContent($asset->dump());

        return $response;
    }

    private function findAssetLeaf(\Traversable $asset, $pos)
    {
        $i = 0;
        foreach ($asset as $leaf) {
            if ($pos == $i++) {
                return $leaf;
            }
        }
        return null;
    }
}
