@component('admin._components.eventContainer', ['event'=>$event])
	<div class="row justify-content-center">
        <div class="col">
         @include("admin._partials.alert")
	        <div class="card">
                <div class="card-header">Settings
                <a href="{{route('events.settings.create',$event)}}"
                   class="btn btn-sm btn-success pull-right">New</a>
                </div>
                
                <div class="table-responsive">
	                <p-table :headers="['Key','Actions']"
	                         :keys="['key']"
	                         :paginator="{{json_encode($settings)}}"
	                         :has-action="true"
	                         search-url="{{route('events.settings.search',$event)}}"
	                         all-url="{{route('events.settings.index',$event)}}"
	                >
		                <template name="action" slot-scope="{item, deleteItem}">
			                <a :href="item.urls.edit"
			                   class="btn btn-info btn-sm text-light">Edit</a>
			                <button :href="item.urls.delete"
			                        @click.prevent="deleteItem(item)"
			                        class="btn btn-danger btn-sm text-light">Delete</button>
		                </template>
	                </p-table>
                </div>
            </div>
        </div>
    </div>
@endcomponent
