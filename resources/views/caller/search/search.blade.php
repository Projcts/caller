   <div class="card bg-white shadow">
       <div class="card-header bg-danger text-white">
           <h3 class="card-title mb-0">Search Call Logs</h3>
       </div>
       <div class="card-body">
           <form method="GET" action="{{ route('caller.caller.getlogs') }}" class="row g-3 mb-4">

               <div class="col-md-3">
                   <label for="user_id" class="form-label">User</label>
                   <select name="user_id" id="user_id" class="form-control">
                       <option value="">Select User</option>
                       @foreach ($users as $key => $user)
                           <option value="{{ $key }}" {{ request('user_id') == $key ? 'selected' : '' }}>
                               {{ $user }}
                           </option>
                       @endforeach
                   </select>
               </div>

               <div class="col-md-3">
                   <label for="lead" class="form-label">Lead</label>
                   <input type="text" name="lead" id="lead" class="form-control"
                       value="{{ request('lead') }}" placeholder="Search Lead ">
               </div>


               <div class="col-md-3">
                   <label for="call_type" class="form-label">Call Type</label>
                   <select name="call_type" id="call_type" class="form-control">
                       <option value="">Select Type</option>
                       @foreach ($calltype as $calltyp)
                           <option value="{{ $calltyp }}" {{ request('call_type') == $calltyp ? 'selected' : '' }}>
                               {{ $calltyp }}
                           </option>
                       @endforeach
                   </select>
               </div>

               <div class="col-md-3">
                   <label for="start_date" class="form-label">Start Date</label>
                   <input type="date" name="start_date" id="start_date" class="form-control"
                       value="{{ request('start_date') }}">
               </div>

               <div class="col-md-3">
                   <label for="end_date" class="form-label">End Date</label>
                   <input type="date" name="end_date" id="end_date" class="form-control"
                       value="{{ request('end_date') }}">
               </div>

               <div class="col-md-12 d-flex justify-content-start align-items-end gap-2">
                   <button type="submit" class="btn btn-primary">
                       <i class="bi bi-search"></i> Search
                   </button>

                   <a href="{{ route('caller.caller.getlogs') }}" class="btn btn-secondary">
                       <i class="bi bi-x-circle"></i> Clear
                   </a>
               </div>

           </form>
       </div>
   </div>
