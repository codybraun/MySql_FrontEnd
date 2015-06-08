Rails.application.routes.draw do
  root 'tables#index'
  resources :tables do
    resources :queries
  end
end
