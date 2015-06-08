class SetSpec < ActiveRecord::Base
  establish_connection "#{Rails.env}_arxiv"
end
