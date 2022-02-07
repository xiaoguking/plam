ALTER  TABLE  `box_market_logs`  ADD  INDEX index_timne_pr (`gettime`,`price`);
ALTER  TABLE  `box_panic_buy`  ADD  INDEX index_timne_pr (`gettime`,`price`);
ALTER  TABLE  `card_market_logs`  ADD  INDEX index_timne_pr (`gettime`,`price`);
ALTER  TABLE  `login`  ADD  INDEX index_timne_pr (`gettime`,`uid`);
ALTER  TABLE  `ol`  ADD  INDEX index_timne_pr (`gettime`,`ol_num`);
ALTER  TABLE  `register`  ADD  INDEX index_timne_pr (`gettime`,`uid`);