#!/bin/bash

folders=(everyone snapshot top total_100 total_250)

cd /data/plattech/tmp/bi_develop_tmp_for_wcl/ga

for var in ${folders[@]};do
ls ${var}/*.txt | grep -v "unit"
mv -f ${var}/*.txt ${var}/old
done

echo "Success."