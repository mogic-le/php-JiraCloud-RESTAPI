<?php

namespace JiraCloud\Group;

/**
 * Class to perform all groups related queries.
 */
class GroupService extends \JiraCloud\JiraClient
{
    private $uri = '/group';

    /**
     * Function to get group.
     *
     * @param array $paramArray Possible values for $paramArray 'username', 'key'.
     *                          "Either the 'username' or the 'key' query parameters need to be provided".
     *
     * @throws \JiraCloud\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group
     */
    public function get($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Group()
        );
    }

    /**
     * Get users from group.
     *
     * @param array $paramArray groupname, includeInactiveUsers, startAt, maxResults
     *
     * @throws \JiraCloud\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return GroupSearchResult
     */
    public function getMembers($paramArray)
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/member'.$queryParam, null);

        $this->log->info("Result=\n".$ret);

        $userData = json_decode($ret);

        $res = $this->json_mapper->map($userData, new GroupSearchResult());

        return $res;
    }

    /**
     * Creates a group by given group parameter.
     *
     * @param \JiraCloud\Group\Group $group
     *
     * @throws \JiraCloud\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group
     */
    public function createGroup(Group $group)
    {
        $data = json_encode($group);

        $ret = $this->exec($this->uri, $data);

        $this->log->info("Result=\n".$ret);

        $group = $this->json_mapper->map(
            json_decode($ret),
            new Group()
        );

        return $group;
    }

    /**
     * Adds given user to a group.
     *
     * @param string $groupName
     * @param string $accountId
     *
     * @throws \JiraCloud\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Group Returns the current state of the group.
     */
    public function addUserToGroup(string $groupName, string $accountId)
    {
        $data = json_encode(['accountId' => $accountId]);

        $ret = $this->exec($this->uri.'/user?groupname='.urlencode($groupName), $data);

        $this->log->info("Result=\n".$ret);

        $group = $this->json_mapper->map(
            json_decode($ret),
            new Group()
        );

        return $group;
    }

    /**
     * Removes given user from a group.
     *
     * @param string $groupName
     * @param string $accountId
     *
     * @throws \JiraCloud\JiraException
     *
     * @return string|null Returns no content
     */
    public function removeUserFromGroup(string $groupName, string $accountId)
    {
        $param = http_build_query(['groupname' => $groupName, 'accountId' => $accountId]);

        $ret = $this->exec($this->uri.'/user?'.$param, [], 'DELETE');

        $this->log->info("Result=\n".$ret);

        return $ret;
    }
}
