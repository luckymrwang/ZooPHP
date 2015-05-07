#!/bin/bash

games=(tank world_war toy_war red_fire war_commander)

cd /data/plattech/tmp/bi_develop_tmp_for_wcl/ga/total
for var in ${games[@]};do
:> ${var}_order_250.txt
for (( i=1; i<250; i=i+50 ));do
if [ -f "${var}_order_${i}.txt" ]
then
echo "${var}_order_${i}.txt"
cat ${var}_order_${i}.txt >> ${var}_order_250.txt
fi
if [ $i == 51 ]
then
cp ${var}_order_250.txt ../total_100/${var}_order_100.txt
fi
done
mv -f ${var}_order_250.txt ../total_250
done

echo "Success."