#!/bin/bash

nsq_url="https://$(docker port nsqor_test 1337)"
while ! curl -s "$nsq_url" --insecure ; do
	echo "Waiting for NSQOR to boot on $nsq_url"
	sleep 1
	test $? -gt 128 && break # Ctrl+c break
done
exit 0
