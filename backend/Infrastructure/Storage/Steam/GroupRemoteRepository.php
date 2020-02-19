<?php /** @noinspection CssInvalidHtmlTagReference */

namespace PlayOrPay\Infrastructure\Storage\Steam;

use Assert\Assert;
use Assert\InvalidArgumentException;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidApiResponseException;
use Knojector\SteamAuthenticationBundle\Exception\InvalidUserClassException;
use Knojector\SteamAuthenticationBundle\Factory\UserFactory;
use Knojector\SteamAuthenticationBundle\Http\SteamApiClient;
use PlayOrPay\Domain\Steam\Group;
use PlayOrPay\Domain\Steam\SteamId;
use PlayOrPay\Infrastructure\Storage\User\UserRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Request;

class GroupRemoteRepository
{
    const BASE_URL = 'https://steamcommunity.com/groups/%s/memberslistxml/?xml=1';

    /** @var ClientInterface */
    private $httpClient;

    /** @var SteamApiClient */
    private $profileClient;

    /** @var GroupRepository */
    private $groupRepo;

    /** @var UserRepository */
    private $userRepo;

    /** @var UserFactory */
    private $userFactory;

    public function __construct(
        ClientInterface $httpClient,
        SteamApiClient $profileClient,
        GroupRepository $groupRepo,
        UserRepository $userRepo,
        UserFactory $userFactory
    ) {
        $this->httpClient = $httpClient;
        $this->profileClient = $profileClient;
        $this->groupRepo = $groupRepo;
        $this->userRepo = $userRepo;
        $this->userFactory = $userFactory;
    }

    /**
     * @param string $code
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     * @throws InvalidApiResponseException
     * @throws InvalidUserClassException
     * @throws Exception
     *
     * @return Group|null
     */
    public function findByCode(string $code): ?Group
    {
        Assert::that($code)->minLength(1);

        $url = sprintf(self::BASE_URL, $code);

        $groupXml = $this->httpClient->request(Request::METHOD_GET, $url)->getBody()->getContents();

        $groupData = new Crawler($groupXml);

        $groupIdNode = $groupData->filter('groupID64')->first();

        if ($groupIdNode->count() === 0) {
            return null;
        }

        $groupId = (int) $groupIdNode->text();
        $groupCode = $groupData->filter('groupURL')->first()->text();
        $groupName = $groupData->filter('groupName')->first()->text();
        $logoUrl = $groupData->filter('avatarFull')->first()->text();

        $group = $this->groupRepo->find($groupId);
        if ($group) {
            $group
                ->setCode($groupCode)
                ->setName($groupName)
                ->setLogoUrl($logoUrl)
                ->clearMembers();
        } else {
            $group = new Group($groupId, $groupCode, $groupName, $logoUrl);
        }

        $memberIds = [];
        $groupData->filter('members steamID64')->each(function (Crawler $member) use (&$memberIds) {
            $memberIds[] = (int) $member->text();
        });

        $memberChunks = array_chunk($memberIds, 100);
        foreach ($memberChunks as $members) {
            foreach ($this->profileClient->loadProfiles($members) as $member) {
                $user = $this->userRepo->find(new SteamId((int) $member['steamid']));
                if ($user) {
                    $user->update($member);
                } else {
                    $user = $this->userFactory->getFromSteamApiResponse($member);
                }

                $group->addMember($user);
            }
        }

        return $group;
    }
}
