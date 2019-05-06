<?php

namespace spec\Akeneo\Tool\Component\Connector\Job;

use PhpSpec\ObjectBehavior;

class JobFileLocationSpec extends ObjectBehavior
{
    function it_is_built_for_local()
    {
        $this->beConstructedWith('/my/path/to/local/file.csv', false);
        $this->isRemote()->shouldReturn(false);
        $this->getPath()->shouldReturn('/my/path/to/local/file.csv');
        $this->encodeLocation()->shouldReturn('/my/path/to/local/file.csv');
    }

    function it_is_built_for_remote()
    {
        $this->beConstructedWith('/my/path/to/remote/file.csv', true);
        $this->isRemote()->shouldReturn(true);
        $this->getPath()->shouldReturn('/my/path/to/remote/file.csv');
        $this->encodeLocation()->shouldReturn('PIM_REMOTE:///my/path/to/remote/file.csv');
    }

    function it_is_built_from_encoded_local_location()
    {
        $this->beConstructedThrough('buildFromEncodedLocation', ['/my/path/to/local/file.csv']);
        $this->isRemote()->shouldReturn(false);
        $this->getPath()->shouldReturn('/my/path/to/local/file.csv');
    }

    function it_is_built_from_encoded_remote_location()
    {
        $this->beConstructedThrough('buildFromEncodedLocation', ['PIM_REMOTE:///my/path/to/remote/file.csv']);
        $this->isRemote()->shouldReturn(true);
        $this->getPath()->shouldReturn('/my/path/to/remote/file.csv');
    }
}
