<?php

require_once(ROOT_DIR . 'Presenters/Admin/ManageResourceGroupsPresenter.php');

class ManageResourceGroupsPresenterTests extends TestBase
{
    /**
     * @var ManageResourceGroupsPresenter
     */
    private $presenter;

    /**
     * @var IManageResourceGroupsPage|PHPUnit_Framework_MockObject_MockObject
     */
    private $page;

    /**
     * @var IResourceRepository|PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceRepository;


    public function setUp(): void
    {
        parent::setup();

        $this->page = $this->createMock('IManageResourceGroupsPage');
        $this->resourceRepository = $this->createMock('IResourceRepository');

        $this->presenter = new ManageResourceGroupsPresenter($this->page, $this->fakeUser, $this->resourceRepository);
    }

    public function testBindsResourcesAndGroupsOnLoad()
    {
        $groupTree = new ResourceGroupTree();
        $resources = [];

        $this->resourceRepository
                ->expects($this->once())
        ->method('GetResourceGroups')
        ->will($this->returnValue($groupTree));

        $this->resourceRepository
                ->expects($this->once())
        ->method('GetResourceList')
        ->will($this->returnValue($resources));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('BindResourceGroups')
        ->with($this->equalTo($groupTree));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('BindResources')
        ->with($this->equalTo($resources));

        $this->presenter->PageLoad();
    }

    public function testAddsResourceToGroup()
    {
        $resourceId = 1;
        $groupId = 2;

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetResourceId')
        ->will($this->returnValue($resourceId));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetGroupId')
        ->will($this->returnValue($groupId));

        $this->resourceRepository
                ->expects($this->once())
        ->method('AddResourceToGroup')
        ->with($this->equalTo($resourceId), $this->equalTo($groupId));

        $this->presenter->AddResource();
    }

    public function testRemovesResourceFromGroup()
    {
        $resourceId = 1;
        $groupId = 2;

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetResourceId')
        ->will($this->returnValue($resourceId));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetGroupId')
        ->will($this->returnValue($groupId));

        $this->resourceRepository
                ->expects($this->once())
        ->method('RemoveResourceFromGroup')
        ->with($this->equalTo($resourceId), $this->equalTo($groupId));

        $this->presenter->RemoveResource();
    }

    public function testMovesGroupNode()
    {
        $nodeId = 1;
        $targetId = 2;
        $nodeType = ResourceGroup::GROUP_TYPE;
        $previousNodeId = 3;

        $group = new ResourceGroup($nodeId, 'name', $previousNodeId);
        $group->MoveTo($targetId);

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetNodeId')
        ->will($this->returnValue($nodeId));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetTargetNodeId')
        ->will($this->returnValue($targetId));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetNodeType')
        ->will($this->returnValue($nodeType));

        $this->page
                ->expects($this->atLeastOnce())
        ->method('GetPreviousNodeId')
        ->will($this->returnValue($previousNodeId));

        $this->resourceRepository
                ->expects($this->once())
        ->method('LoadResourceGroup')
        ->with($this->equalTo($nodeId))
        ->will($this->returnValue($group));

        $this->resourceRepository
                ->expects($this->once())
        ->method('UpdateResourceGroup')
        ->with($group);

        $this->presenter->MoveNode();
    }
}
