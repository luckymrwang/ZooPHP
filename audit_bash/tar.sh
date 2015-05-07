#!/bin/bash

folders=(everyone snapshot top total_100 total_250)

cd /data/plattech/tmp/bi_develop_tmp_for_wcl/ga

for var in ${folders[@]};do
tar -cvzf ${var}/${var}.tar.gz ${var}/*.txt
mv -f ${var}/${var}.tar.gz download
done

echo "Success.\n"