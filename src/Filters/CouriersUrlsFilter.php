<?php

namespace Flagship\Components\Twig\Extensions\Filters;

use Flagship\Components\Entities\Objects\Courier;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CouriersUrlsFilter extends AbstractExtension
{
	public const ICONS = [
		Courier::UPS => ['normal' => '2icon.png'],
		Courier::DHL => ['normal' => '3icon.png'],
		Courier::FEDEX => ['normal' => '4icon.png', 'ground' => '4groundicon.png'],
		Courier::PUROLATOR => ['normal' => '5icon.png'],
		Courier::CANPAR => ['normal' => '6icon.png'],
		Courier::DHLEC => ['normal' => '7icon.png'],
		Courier::GLS => ['normal' => '8icon.png'],
		Courier::TREXITY => ['normal' => '9icon.png'],
		Courier::NATIONEX => ['normal' => '10icon.png'],
		Courier::CANADAPOST => ['normal' => '11icon.png'],
		Courier::GLSUS => ['normal' => '12icon.png'],
	];

	public const URLS = [
		Courier::UPS => 'https://www.ups.com/track?HTMLVersion=5.0&loc=en_CA&Requester=UPSHome&trackNums={token}&track.x=Track',
		Courier::DHL => 'https://www.dhl.com/en/express/tracking.html?AWB={token}&brand=DHL',
		Courier::FEDEX => 'https://www.fedex.com/fedextrack/?tracknumbers={token}&language=en&cntry_code=ca',
		Courier::PUROLATOR => 'https://www.purolator.com/en/app-tracker.page?pins={token}',
		Courier::CANPAR => 'https://www.canpar.com/en/track/TrackingAction.do?reference={token}&locale=en',
		Courier::DHLEC => 'https://www.logistics.dhl/ca-en/home/tracking/tracking-ecommerce.html?tracking-id={token}',
		Courier::GLS => 'https://gls-group.com/CA/en/send-and-receive/track-a-shipment/?match={token}',
		Courier::TREXITY => '{token}',
		Courier::NATIONEX => 'https://www.nationex.com/en/search?id={token}',
		Courier::CANADAPOST => 'https://www.canadapost-postescanada.ca/track-reperage/en#/details/{token}',
		Courier::GLSUS => 'https://gls-group.com/US/en/send-and-receive/track-a-parcel/?match={token}',
	];

	public function getFilters()
    {
        return [
            new TwigFilter('trackUrl', [$this, 'trackUrl']),
			new TwigFilter('iconUrl', [$this, 'iconUrl']),
        ];
    }

	public function iconUrl(int $courierId, string $type = 'normal'): false|string
	{
		return self::ICONS[$courierId][$type] ?? false;
	}

    public function trackUrl(array $data): false|string
    {
		$courierId = $data[0];
		$trackingNumber = $data[1];
		$trackingUrl = $data[2] ?? '';

		if (!array_key_exists($courierId, self::URLS)) {
			return false;
		}

		$token = match ($courierId) {
			Courier::CANPAR => strtok($trackingNumber, '|'),
			Courier::TREXITY => $trackingUrl,
			default => $trackingNumber,
		};

		return str_replace('{token}', $token, self::URLS[$courierId]);
    }
}
