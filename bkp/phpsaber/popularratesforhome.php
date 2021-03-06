<?php
include_once 'workflow/Workflow.php';
include_once 'rest_activities/LeadPriceCalendarActivity.php';
include_once 'data/listofairport.php';
include_once 'data/listwithcode.php';
include_once 'data/datacountrycatipal.php';



        //amadeus data creation starts here  
		      $children = 0;
		      $nonstop = 'false';	

		      $paramsforamadeus['apikey'] = 'SelcXiNfNOcQx9S2GJitUhF9xb81ZoBt';
          
		     
		      if(isset($_REQUEST['lat']) && $_REQUEST['lat'] != "")
          {
            
            $paramsforamadeus['latitude'] = $_REQUEST['lat'];
          }

         

          if(isset($_REQUEST['long']) && $_REQUEST['long'] != "")
          {
            $paramsforamadeus['longitude'] = $_REQUEST['long'];
          }

          
         //http://maps.googleapis.com/maps/api/geocode/json?latlng=30.75,76.8
          
         //$urltosendforamadeus = "https://api.sandbox.amadeus.com/v1.2/airports/nearest-relevant";
         //http://maps.googleapis.com/maps/api/geocode/json?latlng=30.75,76.8
          $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=".$paramsforamadeus['latitude'].",".$paramsforamadeus['longitude'];

          $data = file_get_contents($url);

          $data = json_decode($data);       
          $countrydata = array();
          if(isset($data->results))   
          {

            $datanow = reset($data->results);
            $datanow = $datanow->address_components;
            foreach ($datanow as $key => $value) 
            {
              $type = reset($value->types);
              if($type == 'country')
              {
                $countrydata['long_name']=$value->long_name;
                $countrydata['short_name']=$value->short_name;
              }
            }
          }
          else
          {
            $code = 'NYC';
          }

          echo "<pre>"; print_r($datacapitalcountry[reset($countrydata)]); die;
          //$amadeusresult = getamadeus($urltosendforamadeus, $paramsforamadeus);

        
      
      
      $datatoaddbyamadeus = array();    
      $mainarrayorresulresults = array();
      $mainarrayorresulresults['amadeusresult'] = array();

      //filling main array with amadeus result start
      if(isset($amadeusresult) && $amadeusresult)
      {

        $datatoaddbyamadeus = array();
        $amadeusresult = json_decode($amadeusresult);
        $amadeusresult = (array) $amadeusresult;

        //https://api.sandbox.amadeus.com/v1.2/flights/inspiration-search
        
        //$urltosendforamadeus = "https://api.sandbox.amadeus.com/v1.2/airports/nearest-relevant";

        //$amadeusresult = getamadeus($urltosendforamadeus, $paramsforamadeus);

        $aircraft_movements = array();
        foreach ($amadeusresult as $key => $row)
        {
            
            $aircraft_movements[$key] = $row->aircraft_movements;
        }
        array_multisort($aircraft_movements, SORT_DESC, $amadeusresult);

        $amadeusresultins = array();

        //echo "<pre>"; print_r($amadeusresult); die; 
        foreach ($amadeusresult as $key => $value) 
        {
        
        
          $singleairport = $value;
          
          $origin = $singleairport->airport;

          $urltosendforamadeus = "https://api.sandbox.amadeus.com/v1.2/flights/inspiration-search";

          $paramsforamadeus['origin'] = $origin;

          $paramsforamadeus['one-way'] = 'true';

          
          $amadeusresultins = getamadeus($urltosendforamadeus, $paramsforamadeus);


          $amadeusresultins = json_decode($amadeusresultins);

          if(isset($amadeusresultins->result))
          {
            echo "<pre>"; 

            print_r($amadeusresultins); 

            die;
          }  

        }

        echo "<pre>"; print_r($amadeusresult); die;
        $resultsofamadeustorunloop = isset($amadeusresult['results'])?$amadeusresult['results']:array();
        $resultsofamadeustorunloop = (array)$resultsofamadeustorunloop;
        $checkitinerariescame = reset($resultsofamadeustorunloop);

        if($resultsofamadeustorunloop && isset($checkitinerariescame->itineraries))
        {
          $counterforinoutflightarray = 0;
          foreach ($resultsofamadeustorunloop as $keyitinerary => $value) 
          {
            $value = (array)$value;
            
            $datatoaddbyamadeus[$keyitinerary]['adult'] = $passengercount;
            $datatoaddbyamadeus[$keyitinerary]['wholedataofamadeusoneitinerary'] = $value;
            $datatoaddbyamadeus[$keyitinerary]['whoami'] = "amadeus";
            $datatoaddbyamadeus[$keyitinerary]['totalfare'] = $value['fare']->total_price;
            $datatoaddbyamadeus[$keyitinerary]['totaltax'] = $value['fare']->price_per_adult->tax;
            $datatoaddbyamadeus[$keyitinerary]['totalbeforetax'] = $value['fare']->total_price-$value['fare']->price_per_adult->tax;

            $counterfornoofflights = 1;
            //outbound flights
            $singleresult = reset($value['itineraries']);
            $outbound = $singleresult->outbound->flights;
            $inbound = isset($singleresult->inbound)?$singleresult->inbound->flights:array();
            
            $datatoaddbyamadeus[$keyitinerary]['nonstoporwithstopoutbound'] = count($outbound)>1?count($outbound)." Stop":"Non Stop";
            $datatoaddbyamadeus[$keyitinerary]['nonstoporwithstopinbound'] = count($inbound)>1?(count($inbound)-1)." Stop":"Non Stop";

             //filter for no of stops
             $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = 0;
           
            $forfiltercheck=1; 
            $counterforoutbound =0;
            foreach ($outbound as $keyout => $valueout) 
            {
            
               
              //for unique identification
              $keyforflightno = 'outfn'.$keyout;
              $datatoaddbyamadeus[$keyitinerary][$keyforflightno] = $valueout->flight_number;

              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->flight_number;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['operatingairlinecode'] = $valueout->operating_airline;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['counterfornoofflights'] = $counterfornoofflights;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinefullname'] = $listofairports[$valueout->marketing_airline];
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = isset($listwithcode[$valueout->origin->airport])?$listwithcode[$valueout->origin->airport]:$valueout->origin->airport; 
     
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['destinationairportfullname'] = isset($listwithcode[$valueout->destination->airport])?$listwithcode[$valueout->destination->airport]:$valueout->destination->airport;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['travelclass'] = $valueout->booking_info->travel_class;
             
                //layovertime
                $layovertime=0;
                $layovertimeinseconds=0;
                //if outbound has count 1 means non connection flight means no layovertime
                if(count($outbound)>1)
                {
                  
                    $currentflight =   $valueout;
                    $nextflight = isset($outbound[$counterforoutbound+1])?$outbound[$counterforoutbound+1]:array();
                    if($nextflight)
                    {
                      $currentflightarraiveat = strtotime($currentflight->arrives_at);
                      $nextflightdepartat = strtotime($nextflight->departs_at); 
                      $layovertime=$nextflightdepartat-$currentflightarraiveat;
                      $layovertimeinseconds=$layovertime;
                      $timearray = secondsToTime($layovertime);
                      $layovertime  = reset($timearray)."h".next($timearray)."m";
                    }
                  $counterforoutbound++;  
                }

              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertime'] = $layovertime;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinecode'] = $valueout->marketing_airline;
           
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightseq'] = $keyout+1; 
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['logoofmarketingairline'] = $valueout->marketing_airline.".png"; 
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['nameofmarketingairline'] = $listofairports[$valueout->marketing_airline];
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departtime'] = $valueout->departs_at; 
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departureairportfullname'] = isset($listwithcode[$valueout->origin->airport])?$listwithcode[$valueout->origin->airport]:$valueout->origin->airport;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivalairportfullname'] = isset($listwithcode[$valueout->destination->airport])?$listwithcode[$valueout->destination->airport]:$valueout->destination->airport;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivaltime'] = $valueout->arrives_at;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->flight_number;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['stopornonstop'] = count($outbound)>1?(count($outbound)-1)." Stop":"Non Stop";
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['returnorarrival'] = "outbound";
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertimeinsec'] = $layovertimeinseconds;

              //filter for no of stops
              $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] + 1;

              //layover time in sec for filter  
              $datatoaddbyamadeus[$keyitinerary]['layovertimeinsec'][] = $layovertimeinseconds;

              //gathering data for array unique
              $datatoaddbyamadeus[$keyitinerary]['flightnoarray'][] = $valueout->flight_number; 
              $datatoaddbyamadeus[$keyitinerary]['marketingairlinearray'][] = $valueout->marketing_airline; 
              $datatoaddbyamadeus[$keyitinerary]['originarray'][] = $valueout->origin->airport;
              $datatoaddbyamadeus[$keyitinerary]['destinationarray'][] =$valueout->destination->airport;

              if($forfiltercheck==1)
              {  
                //filter for departure date
                $datatoaddbyamadeus[$keyitinerary]['outdeparturedate'] = strtotime($valueout->departs_at);
                $datatoaddbyamadeus[$keyitinerary]['marketingairlinecodeout'] = $valueout->marketing_airline;
              }  
              
              $forfiltercheck++;
              $counterforinoutflightarray++;
              $counterfornoofflights++;
            }//outbound flights loop  

            $forfiltercheck=1;
            //loop for inbound flights  
            $counterforoutbound =0;
            foreach ($inbound as $keyout => $valueout) 
            {
            
              //for unique identification
              $keyforflightno = 'infn'.$keyout;
              $datatoaddbyamadeus[$keyitinerary][$keyforflightno] = $valueout->flight_number;
               
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->flight_number;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['operatingairlinecode'] = $valueout->operating_airline;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['counterfornoofflights'] = $counterfornoofflights;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinefullname'] = $listofairports[$valueout->marketing_airline];
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = isset($listwithcode[$valueout->origin->airport])?$listwithcode[$valueout->origin->airport]:$valueout->origin->airport; 
     
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['destinationairportfullname'] = isset($listwithcode[$valueout->destination->airport])?$listwithcode[$valueout->destination->airport]:$valueout->destination->airport;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['travelclass'] = $valueout->booking_info->travel_class;
             
                //layovertime
                $layovertime=0;
                $layovertimeinseconds=0;
                //if outbound has count 1 means non connection flight means no layovertime
                if(count($inbound)>1)
                {
                  
                    $currentflight =   $valueout;
                    $nextflight = isset($inbound[$counterforoutbound+1])?$inbound[$counterforoutbound+1]:array();
                    if($nextflight)
                    {
                      $currentflightarraiveat = strtotime($currentflight->arrives_at);
                      $nextflightdepartat = strtotime($nextflight->departs_at); 
                      $layovertime=$nextflightdepartat-$currentflightarraiveat;
                      $layovertimeinseconds=$layovertime;
                      $timearray = secondsToTime($layovertime);
                      $layovertime  = reset($timearray)."h".next($timearray)."m";
                    }
                  $counterforoutbound++;  
                }

              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertime'] = $layovertime;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinecode'] = $valueout->marketing_airline;
           
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightseq'] = $keyout+1; 
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['logoofmarketingairline'] = $valueout->marketing_airline.".png"; 
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['nameofmarketingairline'] = $listofairports[$valueout->marketing_airline];
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departtime'] = $valueout->departs_at; 
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departureairportfullname'] = isset($listwithcode[$valueout->origin->airport])?$listwithcode[$valueout->origin->airport]:$valueout->origin->airport;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivalairportfullname'] = isset($listwithcode[$valueout->destination->airport])?$listwithcode[$valueout->destination->airport]:$valueout->destination->airport;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivaltime'] = $valueout->arrives_at;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->flight_number;
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['stopornonstop'] = count($inbound)>1?(count($inbound)-1)." Stop":"Non Stop";
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['returnorarrival'] = "inbound";
              $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertimeinsec'] = $layovertimeinseconds;

              //filter for no of stops
              $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] + 1;

              //layover time in sec for filter  
              $datatoaddbyamadeus[$keyitinerary]['layovertimeinsec'][] = $layovertimeinseconds;

              //gathering data for array unique
              $datatoaddbyamadeus[$keyitinerary]['flightnoarray'][] = $valueout->flight_number; 
              $datatoaddbyamadeus[$keyitinerary]['marketingairlinearray'][] = $valueout->marketing_airline; 
              $datatoaddbyamadeus[$keyitinerary]['originarray'][] = $valueout->origin->airport;
              $datatoaddbyamadeus[$keyitinerary]['destinationarray'][] =$valueout->destination->airport;


              if($forfiltercheck==1)
              {  
                //filter for departure date
                $datatoaddbyamadeus[$keyitinerary]['indeparturedate'] = strtotime($valueout->departs_at);
                $datatoaddbyamadeus[$keyitinerary]['marketingairlinecodein'] = $valueout->marketing_airline;
              }
              
              $forfiltercheck++;
              $counterforinoutflightarray++;
              $counterfornoofflights++;
            }//inbound flights loop 
           
           $datatoaddbyamadeus[$keyitinerary]['totalflightcounter'] = $counterfornoofflights-1;
          }//for loop $resultsofamadeustorunloop
      
        }//if of $resultsofamadeustorunloop
        

      }
      //filling main array with amadeus result ends

      $mainarrayorresulresults['amadeusresult'] = $datatoaddbyamadeus;


      //running saber api and getting results
      $result = array();
		  //$result = $workflow->runWorkflow();

      $arrayofres = array();

		  //$arrayofres = (array) $result;

		  if($arrayofres)
		  {
		   	
  			$bypassfirstelementofarray = reset($arrayofres);
  			$leadpricecalanderresult = $bypassfirstelementofarray['LeadPriceCalendar'];
        $resultofinstaflights = $bypassfirstelementofarray['InstaFlight'];

        if(!isset($resultofinstaflights->errorCode))
        { 
          $datatoaddbyamadeus = array();
          $instaflightresults = $resultofinstaflights->PricedItineraries;
          $counterforinoutflightarray = 0;
          foreach ($instaflightresults as $keyitinerary => $value) 
          {

                  $value = (array)$value;

                  $fareinfo = $value['AirItineraryPricingInfo'];  
                  $fareinfoforcabin = $fareinfo->FareInfos->FareInfo;
                 
                  $fareinfosimplefied = $fareinfo->PTC_FareBreakdowns->PTC_FareBreakdown->PassengerFare;
                  $value = $value['AirItinerary'];  
                  
                  $datatoaddbyamadeus[$keyitinerary]['adult'] = $passengercount;
                  $datatoaddbyamadeus[$keyitinerary]['wholedataofamadeusoneitinerary'] = $value;
                  $datatoaddbyamadeus[$keyitinerary]['whoami'] = "sabreinsta";
                  $datatoaddbyamadeus[$keyitinerary]['totalfare'] = $fareinfosimplefied->TotalFare->Amount;
                  $datatoaddbyamadeus[$keyitinerary]['totaltax'] = $fareinfosimplefied->Taxes->TotalTax->Amount;
                  $datatoaddbyamadeus[$keyitinerary]['totalbeforetax'] = $fareinfosimplefied->FareConstruction->Amount;
                  
                  $counterfornoofflights = 1;
                  //outbound flights
                  
                  $flightsegment = $value->OriginDestinationOptions->OriginDestinationOption;
                  
                  
                  $singleresult = $flightsegment;
                  $outbound = reset($singleresult);
                  $outboundtochecknonstop = $outbound;
                  $outbound = $outbound->FlightSegment;
                  $inbound = isset($singleresult[1])?next($singleresult):array();
                  $inboundtochecknonstop = $inbound; 
                  $inbound = isset($singleresult[1])?$inbound->FlightSegment:array();
                  
                  $datatoaddbyamadeus[$keyitinerary]['nonstoporwithstopoutbound'] = count($outboundtochecknonstop->FlightSegment)>1?(count($outboundtochecknonstop->FlightSegment)-1)." Stop":"Non Stop";
                  $datatoaddbyamadeus[$keyitinerary]['nonstoporwithstopinbound'] = count($inboundtochecknonstop->FlightSegment)>1?(count($inboundtochecknonstop->FlightSegment)-1)." Stop":"Non Stop";

                  $outboundcheckstopnonstop = $outboundtochecknonstop->FlightSegment;
                  $inboundstopnonstopcheck = $inboundtochecknonstop->FlightSegment;

                  //filter for no of stops
                  $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = 0;
                 
                  
                  $forfiltercheck=1;
                  $counterforoutbound =0;
                  foreach ($outbound as $keyout => $valueout) 
                  {
                    
                    //for unique identification
                    $keyforflightno = 'outfn'.$keyout;
                    $datatoaddbyamadeus[$keyitinerary][$keyforflightno] = $valueout->FlightNumber;

                     //print_r($outbound); die;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->FlightNumber;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['operatingairlinecode'] = $valueout->OperatingAirline->Code;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['counterfornoofflights'] = $counterfornoofflights;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinefullname'] = $listofairports[$valueout->MarketingAirline->Code];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = isset($listwithcode[$valueout->DepartureAirport->LocationCode])?$listwithcode[$valueout->DepartureAirport->LocationCode]:$valueout->DepartureAirport->LocationCode; 
           
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['destinationairportfullname'] = isset($listwithcode[$valueout->ArrivalAirport->LocationCode])?$listwithcode[$valueout->ArrivalAirport->LocationCode]:$valueout->ArrivalAirport->LocationCode;

                    //getting each flight class
                    $saberclassarray = array();
                    $saberclassarray['P'] = strtoupper("Premium");

                    $saberclassarray['F'] = strtoupper("First");

                    $saberclassarray['J'] = strtoupper("Premium Business");

                    $saberclassarray['C'] = strtoupper("Business");

                    $saberclassarray['S'] = strtoupper("Premium Economy");

                    $saberclassarray['Y'] = "ECONOMY";

                    $classofflight = isset($fareinfoforcabin[$keyout])?$fareinfoforcabin[$keyout]->TPA_Extensions->Cabin->Cabin:"";

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['travelclass'] = $classofflight!=""?$saberclassarray[$classofflight]:"";
                   
                    $totaltimeinthisflightsegment = isset($singleresult[$counterforinoutflightarray])?$singleresult[$counterforinoutflightarray]->ElapsedTime:0;
                    
                      //layovertime
                      $layovertime=0;
                      $layovertimeinseconds=0;
                      //if outbound has count 1 means non connection flight means no layovertime
                    
                      if(count($outbound)>1)
                      {
                        
                          $currentflight =   $valueout;
                          $nextflight = isset($outbound[$counterforoutbound+1])?$outbound[$counterforoutbound+1]:array();
                      
                          if($nextflight)
                          {
                            $currentflightarraiveat = strtotime($currentflight->ArrivalDateTime);
                            $nextflightdepartat = strtotime($nextflight->DepartureDateTime); 
                            $layovertime=$nextflightdepartat-$currentflightarraiveat;
                            $layovertimeinseconds=$layovertime;
                            $timearray = secondsToTime($layovertime);
                            $layovertime  = reset($timearray)."h".next($timearray)."m";
                          }
                        $counterforoutbound++;  
                      }

                     
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertime'] = $layovertime;

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinecode'] = $valueout->MarketingAirline->Code;
                 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightseq'] = $keyout+1; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['logoofmarketingairline'] = $valueout->MarketingAirline->Code.".png"; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['nameofmarketingairline'] = $listofairports[$valueout->MarketingAirline->Code];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departtime'] = $valueout->DepartureDateTime; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departureairportfullname'] = isset($listwithcode[$valueout->DepartureAirport->LocationCode])?$listwithcode[$valueout->DepartureAirport->LocationCode]:$valueout->DepartureAirport->LocationCode;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivalairportfullname'] = isset($listwithcode[$valueout->ArrivalAirport->LocationCode])?$listwithcode[$valueout->ArrivalAirport->LocationCode]:$valueout->ArrivalAirport->LocationCode;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivaltime'] = $valueout->ArrivalDateTime;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->FlightNumber;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['stopornonstop'] = count($outboundcheckstopnonstop)>1?(count($outboundcheckstopnonstop)-1)." Stop":"Non Stop";
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['returnorarrival'] = "outbound";
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertimeinsec'] = $layovertimeinseconds;

                    //filter for no of stops
                    $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] + 1;

                    //layover time in sec for filter  
                    $datatoaddbyamadeus[$keyitinerary]['layovertimeinsec'][] = $layovertimeinseconds;

                    //gathering data for array unique
                    $datatoaddbyamadeus[$keyitinerary]['flightnoarray'][] = $valueout->FlightNumber;  
                    $datatoaddbyamadeus[$keyitinerary]['marketingairlinearray'][] = $valueout->MarketingAirline->Code;  
                    $datatoaddbyamadeus[$keyitinerary]['originarray'][] = $valueout->DepartureAirport->LocationCode;
                    $datatoaddbyamadeus[$keyitinerary]['destinationarray'][] = $valueout->ArrivalAirport->LocationCode;


                    if($forfiltercheck==1)
                    {  
                      //filter for departure date
                      $datatoaddbyamadeus[$keyitinerary]['outdeparturedate'] = strtotime($valueout->DepartureDateTime);
                      $datatoaddbyamadeus[$keyitinerary]['marketingairlinecodeout'] = $valueout->MarketingAirline->Code;
                    }
                    
                    $forfiltercheck++;
                    $counterforinoutflightarray++;
                    $counterfornoofflights++;
                  }//outbound flights loop  

                  $forfiltercheck=1;
                  //loop for inbound flights  
                  $counterforoutbound =0;
                  foreach ($inbound as $keyout => $valueout) 
                  {
                    
                    //for unique identification
                    $keyforflightno = 'infn'.$keyout;
                    $datatoaddbyamadeus[$keyitinerary][$keyforflightno] = $valueout->FlightNumber;
                     
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->FlightNumber;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['operatingairlinecode'] = $valueout->OperatingAirline->Code;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['counterfornoofflights'] = $counterfornoofflights;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinefullname'] = $listofairports[$valueout->MarketingAirline->Code];

                   $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = isset($listwithcode[$valueout->DepartureAirport->LocationCode])?$listwithcode[$valueout->DepartureAirport->LocationCode]:$valueout->DepartureAirport->LocationCode; 
           
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['destinationairportfullname'] = isset($listwithcode[$valueout->ArrivalAirport->LocationCode])?$listwithcode[$valueout->ArrivalAirport->LocationCode]:$valueout->ArrivalAirport->LocationCode;

                    //getting each flight class
                    $saberclassarray = array();
                    $saberclassarray['P'] = strtoupper("Premium");

                    $saberclassarray['F'] = strtoupper("First");

                    $saberclassarray['J'] = strtoupper("Premium Business");

                    $saberclassarray['C'] = strtoupper("Business");

                    $saberclassarray['S'] = strtoupper("Premium Economy");

                    $saberclassarray['Y'] = "ECONOMY";

                    $classofflight = isset($fareinfoforcabin[$keyout])?$fareinfoforcabin[$keyout]->TPA_Extensions->Cabin->Cabin:"";

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['travelclass'] = $classofflight!=""?$saberclassarray[$classofflight]:"";
                   
                    $totaltimeinthisflightsegment = isset($singleresult[$counterforinoutflightarray])?$singleresult[$counterforinoutflightarray]->ElapsedTime:0;
                   
                      //layovertime
                      $layovertime=0;
                      $layovertimeinseconds=0;
                      //if outbound has count 1 means non connection flight means no layovertime
                      if(count($inbound)>1)
                      {
                        
                          $currentflight =   $valueout;
                          $nextflight = isset($inbound[$counterforoutbound+1])?$inbound[$counterforoutbound+1]:array();
                          if($nextflight)
                          {
                            $currentflightarraiveat = strtotime($currentflight->ArrivalDateTime);
                            $nextflightdepartat = strtotime($nextflight->DepartureDateTime); 
                            $layovertime=$nextflightdepartat-$currentflightarraiveat;
                            $layovertimeinseconds=$layovertime;
                            $timearray = secondsToTime($layovertime);
                            $layovertime  = reset($timearray)."h".next($timearray)."m";
                          }
                        $counterforoutbound++;  
                      }

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertime'] = $layovertime;

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinecode'] = $valueout->MarketingAirline->Code;
                 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightseq'] = $keyout+1; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['logoofmarketingairline'] = $valueout->MarketingAirline->Code.".png"; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['nameofmarketingairline'] = $listofairports[$valueout->MarketingAirline->Code];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departtime'] = $valueout->DepartureDateTime; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departureairportfullname'] = isset($listwithcode[$valueout->DepartureAirport->LocationCode])?$listwithcode[$valueout->DepartureAirport->LocationCode]:$valueout->DepartureAirport->LocationCode;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivalairportfullname'] = isset($listwithcode[$valueout->ArrivalAirport->LocationCode])?$listwithcode[$valueout->ArrivalAirport->LocationCode]:$valueout->ArrivalAirport->LocationCode;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivaltime'] = $valueout->ArrivalDateTime;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout->FlightNumber;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['stopornonstop'] = count($inboundstopnonstopcheck)>1?(count($inboundstopnonstopcheck)-1)." Stop":"Non Stop";
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['returnorarrival'] = "inbound";
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertimeinsec'] = $layovertimeinseconds;

                    //filter for no of stops
                    $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] + 1;

                    //layover time in sec for filter  
                    $datatoaddbyamadeus[$keyitinerary]['layovertimeinsec'][] = $layovertimeinseconds;

                    //gathering data for array unique
                    $datatoaddbyamadeus[$keyitinerary]['flightnoarray'][] = $valueout->FlightNumber;  
                    $datatoaddbyamadeus[$keyitinerary]['marketingairlinearray'][] = $valueout->MarketingAirline->Code;  
                    $datatoaddbyamadeus[$keyitinerary]['originarray'][] = $valueout->DepartureAirport->LocationCode;
                    $datatoaddbyamadeus[$keyitinerary]['destinationarray'][] = $valueout->ArrivalAirport->LocationCode;


                    if($forfiltercheck==1)
                    {  
                      //filter for departure date
                      $datatoaddbyamadeus[$keyitinerary]['indeparturedate'] = strtotime($valueout->DepartureDateTime);
                      $datatoaddbyamadeus[$keyitinerary]['marketingairlinecodein'] = $valueout->MarketingAirline->Code;
                    }
                    
                    $forfiltercheck++;
                    $counterforinoutflightarray++;
                    $counterfornoofflights++;
                  }//inbound flights loop 
                 
                 $datatoaddbyamadeus[$keyitinerary]['totalflightcounter'] = $counterfornoofflights-1;
            
          }//for each ofinsta flights
        }//if instan flight error code
  			//echo json_encode($leadpricecalanderresult);
        //echo count($mainarrayorresulresults['amadeusresult']);
        //echo count($datatoaddbyamadeus);
        $mainarrayorresulresults['amadeusresult'] = array_merge($mainarrayorresulresults['amadeusresult'], $datatoaddbyamadeus);
        //echo count($mainarrayorresulresults['amadeusresult']);
        //print_r($datatoaddbyamadeus);

        
		  }//if arrayofres ends here


   
      //bargain finder max started

      include_once 'soap_activities/BargainFinderMaxSoapActivity.php';


      //$workflow = new Workflow(new BargainFinderMaxSoapActivity());
      //$resultbfm = $workflow->runWorkflow();

      $arrayofresbfm = array();

      //$arrayofresbfm = (array) $resultbfm;

      if($arrayofresbfm)
      {

        $datatoaddbyamadeus = array();
       
        $bypassfirstelementofarraybfm = reset($arrayofresbfm);
         

        $requestofxmlbfm = $bypassfirstelementofarraybfm['BargainFinderMaxRQ'];
        
        $arraybfmrequestarray = XML2Array::createArray($requestofxmlbfm);

        $responseofbfm = $bypassfirstelementofarraybfm['BargainFinderMaxRS'];

        $arraybfmresponsearray = XML2Array::createArray($responseofbfm);
        
        $arraybfmresponsearray = reset($arraybfmresponsearray);  //[SOAP-ENV:Envelope]

        $arraybfmresponsearray = $arraybfmresponsearray['SOAP-ENV:Body'];
       
        $arraybfmresponsearray = reset($arraybfmresponsearray); // [OTA_AirLowFareSearchRS]
        
        if(isset($arraybfmresponsearray['PricedItineraries']))
        {  
          $arraybfmresponsearray = $arraybfmresponsearray['PricedItineraries']; 
          $arraybfmresponsearray = $arraybfmresponsearray['PricedItinerary']; 
          
          $bfmtresults = $arraybfmresponsearray;
        }else{ $bfmtresults = array(); }  

          $counterforinoutflightarray = 0;
          foreach ($bfmtresults as $keyitinerary => $value) 
          {
                
                $fareinfo = $value['AirItineraryPricingInfo'];  
               
                $fareinfoforcabin = $fareinfo['FareInfos']['FareInfo'];
               
                $fareinfosimplefied = $fareinfo['PTC_FareBreakdowns']['PTC_FareBreakdown']['PassengerFare'];
                $value = $value['AirItinerary'];  
                
                $datatoaddbyamadeus[$keyitinerary]['adult'] = $passengercount;
                $datatoaddbyamadeus[$keyitinerary]['wholedataofamadeusoneitinerary'] = $value;
                $datatoaddbyamadeus[$keyitinerary]['whoami'] = "sabrebfm";
                $datatoaddbyamadeus[$keyitinerary]['totalfare'] = $fareinfosimplefied['TotalFare']['@attributes']['Amount'];
                $datatoaddbyamadeus[$keyitinerary]['totaltax'] = $fareinfosimplefied['Taxes']['TotalTax']['@attributes']['Amount'];
                $datatoaddbyamadeus[$keyitinerary]['totalbeforetax'] = $fareinfosimplefied['FareConstruction']['@attributes']['Amount'];

                $counterfornoofflights = 1;
                  //outbound flights
                  
                $flightsegment = $value['OriginDestinationOptions']['OriginDestinationOption'];
                  
                 
                $singleresult = $flightsegment;
                
                
                
                $outbound = $singleresult;
              
                //one way flight
                //case one outbound flighs have no of flights
                  if(isset($outbound['FlightSegment']) && isset($outbound['FlightSegment']['DepartureAirport'])) //direct flight
                  {

                    $outboundtochecknonstop = array(0=>$outbound['FlightSegment']);
                    $outbound = array(0=>$outbound['FlightSegment']);
                  //case two outbound flighs have one flight 
                  }elseif(isset($outbound['FlightSegment']) && isset($outbound['FlightSegment'][0]['DepartureAirport'])){ //not direct flight
                    
                                       
                    $outbound = $outbound['FlightSegment'];
                  }
                //return flight  
                  //case one outbound flighs have no of flights
                  if(isset($outbound[0]['FlightSegment']) && isset($outbound[0]['FlightSegment']['DepartureAirport'])) //direct flight
                  {

                    $outboundtochecknonstop = array(0=>$outbound[0]['FlightSegment']);
                    $outbound = array(0=>$outbound[0]['FlightSegment']);
                  //case two outbound flighs have one flight 
                  }elseif(isset($outbound[0]['FlightSegment']) && isset($outbound[0]['FlightSegment'][0]['DepartureAirport'])){ //not direct flight
                    
                    $outbound = reset($singleresult);
                    
                    $outbound = $outbound['FlightSegment'];
                  }


                $inbound = isset($singleresult[1])?next($singleresult):array();//yes reaturn flight there

                if($inbound)
                {  
                  $inboundtochecknonstop = isset($singleresult[1])&&isset($inbound['FlightSegment'])&&isset($inbound['FlightSegment']['DepartureAirport'])?array(0=>$inbound['FlightSegment']):$inbound['FlightSegment'];
                  $inbound = isset($singleresult[1])&&isset($inbound['FlightSegment'])&&isset($inbound['FlightSegment']['DepartureAirport'])?array(0=>$inbound['FlightSegment']):$inbound['FlightSegment'];
                }  
                else
                {
                  $inboundtochecknonstop = array();
                  $inbound = array();

                }
             
                $datatoaddbyamadeus[$keyitinerary]['nonstoporwithstopoutbound'] = " Stop";
                $datatoaddbyamadeus[$keyitinerary]['nonstoporwithstopinbound'] = "Non Stop";

                //filter for no of stops
                $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = 0;

                  $forfiltercheck = 1;
                  $counterforoutbound =0;
                  foreach ($outbound as $keyout => $valueout) 
                  {
                    
                    if(isset($valueout['@attributes']['FlightNumber']))
                    {
                        
                        //for unique identification
                        $keyforflightno = 'outfn'.$keyout;
                        $datatoaddbyamadeus[$keyitinerary][$keyforflightno] = $valueout['@attributes']['FlightNumber'];
                    
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout['@attributes']['FlightNumber'];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['operatingairlinecode'] = $valueout['OperatingAirline']['@attributes']['Code'];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['counterfornoofflights'] = $counterfornoofflights;
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinefullname'] = $listofairports[$valueout['MarketingAirline']['@attributes']['Code']];
                        //$datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = $listwithcode[$valueout['DepartureAirport']['@attributes']['LocationCode']]; 
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = $valueout['DepartureAirport']['@attributes']['LocationCode']; 
               
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['destinationairportfullname'] = isset($listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']])?$listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']]:$valueout['ArrivalAirport']['@attributes']['LocationCode'];

                        //getting each flight class
                        $saberclassarray = array();
                        $saberclassarray['P'] = strtoupper("Premium");

                        $saberclassarray['F'] = strtoupper("First");

                        $saberclassarray['J'] = strtoupper("Premium Business");

                        $saberclassarray['C'] = strtoupper("Business");

                        $saberclassarray['S'] = strtoupper("Premium Economy");

                        $saberclassarray['Y'] = "ECONOMY";
                        
                        $classofflight = isset($fareinfoforcabin[$keyout])?$fareinfoforcabin[$keyout]['TPA_Extensions']['Cabin']['@attributes']['Cabin']:"";

                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['travelclass'] = $classofflight!=""?$saberclassarray[$classofflight]:"";
                       
                        //layovertime
                          $layovertime=0;
                          $layovertimeinseconds=0;
                          //if outbound has count 1 means non connection flight means no layovertime
                          if(count($outbound)>1)
                          {
                            
                              $currentflight =   $valueout;
                              $nextflight = isset($outbound[$counterforoutbound+1])?$outbound[$counterforoutbound+1]:array();
                              if($nextflight)
                              {
                                $currentflightarraiveat = strtotime($currentflight['@attributes']['ArrivalDateTime']);
                                $nextflightdepartat = strtotime($nextflight['@attributes']['DepartureDateTime']); 
                                $layovertime=$nextflightdepartat-$currentflightarraiveat;
                                $layovertimeinseconds=$layovertime;
                                $timearray = secondsToTime($layovertime);
                                $layovertime  = reset($timearray)."h".next($timearray)."m";
                              }
                            $counterforoutbound++;  
                          }

                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertime'] = $layovertime;
                       
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinecode'] = $valueout['MarketingAirline']['@attributes']['Code'];
                     
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightseq'] = $keyout+1; 
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['logoofmarketingairline'] = $valueout['MarketingAirline']['@attributes']['Code'].".png"; 
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['nameofmarketingairline'] = $listofairports[$valueout['MarketingAirline']['@attributes']['Code']];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departtime'] = $valueout['@attributes']['DepartureDateTime']; 
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departureairportfullname'] = isset($listwithcode[$valueout['DepartureAirport']['@attributes']['LocationCode']])?$listwithcode[$valueout['DepartureAirport']['@attributes']['LocationCode']]:$valueout['DepartureAirport']['@attributes']['LocationCode'];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivalairportfullname'] = isset($listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']])?$listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']]:$valueout['ArrivalAirport']['@attributes']['LocationCode'];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivaltime'] = $valueout['@attributes']['ArrivalDateTime'];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout['@attributes']['FlightNumber'];
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['stopornonstop'] = count($outbound)==1?"Non Stop":(count($outbound)-1)." Stop";
                       
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['returnorarrival'] = "outbound";
                        $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertimeinsec'] = $layovertimeinseconds;

                        //filter for no of stops
                        $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] + 1;

                        //layover time in sec for filter  
                        $datatoaddbyamadeus[$keyitinerary]['layovertimeinsec'][] = $layovertimeinseconds;

                        //gathering data for array unique
                        $datatoaddbyamadeus[$keyitinerary]['flightnoarray'][] = $valueout['@attributes']['FlightNumber'];  
                        $datatoaddbyamadeus[$keyitinerary]['marketingairlinearray'][] = $valueout['MarketingAirline']['@attributes']['Code'];  
                        $datatoaddbyamadeus[$keyitinerary]['originarray'][] = $valueout['DepartureAirport']['@attributes']['LocationCode'];
                        $datatoaddbyamadeus[$keyitinerary]['destinationarray'][] = $valueout['ArrivalAirport']['@attributes']['LocationCode'];  

                        if($forfiltercheck==1)
                        {  
                          //filter for departure date
                          $datatoaddbyamadeus[$keyitinerary]['outdeparturedate'] = strtotime($valueout['@attributes']['DepartureDateTime']);
                          $datatoaddbyamadeus[$keyitinerary]['marketingairlinecodeout'] = $valueout['MarketingAirline']['@attributes']['Code'];
                        }
                        
                        $forfiltercheck++;
                        $counterforinoutflightarray++;
                        $counterfornoofflights++;
                    }//if ends check attribute flight number exists    
                   
                  }//foreach out bound ends here


                     //loop for inbound flights  
                  $forfiltercheck  = 0;
                  $counterforoutbound =0;
                  foreach ($inbound as $keyout => $valueout) 
                  {
                  
                    //for unique identification
                    $keyforflightno = 'infn'.$keyout;
                    $datatoaddbyamadeus[$keyitinerary][$keyforflightno] = $valueout['@attributes']['FlightNumber'];


                     
                  $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout['@attributes']['FlightNumber'];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['operatingairlinecode'] = $valueout['OperatingAirline']['@attributes']['Code'];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['counterfornoofflights'] = $counterfornoofflights;
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinefullname'] = $listofairports[$valueout['MarketingAirline']['@attributes']['Code']];
                    //$datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = $listwithcode[$valueout['DepartureAirport']['@attributes']['LocationCode']]; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['originairportfullname'] = $valueout['DepartureAirport']['@attributes']['LocationCode']; 
           
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['destinationairportfullname'] = isset($listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']])?$listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']]:$valueout['ArrivalAirport']['@attributes']['LocationCode'];

                     //getting each flight class
                    $saberclassarray = array();
                    $saberclassarray['P'] = strtoupper("Premium");

                    $saberclassarray['F'] = strtoupper("First");

                    $saberclassarray['J'] = strtoupper("Premium Business");

                    $saberclassarray['C'] = strtoupper("Business");

                    $saberclassarray['S'] = strtoupper("Premium Economy");

                    $saberclassarray['Y'] = "ECONOMY";
                    
                    $classofflight = isset($fareinfoforcabin[$keyout])?$fareinfoforcabin[$keyout]['TPA_Extensions']['Cabin']['@attributes']['Cabin']:"";

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['travelclass'] = $classofflight!=""?$saberclassarray[$classofflight]:"";
                   
                 
                       //layovertime
                      $layovertime=0;
                      $layovertimeinseconds=0;
                      //if outbound has count 1 means non connection flight means no layovertime
                      if(count($inbound)>1)
                      {
                        
                          $currentflight =   $valueout;
                          $nextflight = isset($inbound[$counterforoutbound+1])?$inbound[$counterforoutbound+1]:array();
                          if($nextflight)
                          {
                            $currentflightarraiveat = strtotime($currentflight['@attributes']['ArrivalDateTime']);
                            $nextflightdepartat = strtotime($nextflight['@attributes']['DepartureDateTime']); 
                            $layovertime=$nextflightdepartat-$currentflightarraiveat;
                            $layovertimeinseconds=$layovertime;
                            $timearray = secondsToTime($layovertime);
                            $layovertime  = reset($timearray)."h".next($timearray)."m";
                          }
                        $counterforoutbound++;  
                      }

                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertime'] = $layovertime;
                   
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['marketingairlinecode'] = $valueout['MarketingAirline']['@attributes']['Code'];
                 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightseq'] = $keyout+1; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['logoofmarketingairline'] = $valueout['MarketingAirline']['@attributes']['Code'].".png"; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['nameofmarketingairline'] = $listofairports[$valueout['MarketingAirline']['@attributes']['Code']];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departtime'] = $valueout['@attributes']['DepartureDateTime']; 
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['departureairportfullname'] = isset($listwithcode[$valueout['DepartureAirport']['@attributes']['LocationCode']])?$listwithcode[$valueout['DepartureAirport']['@attributes']['LocationCode']]:$valueout['DepartureAirport']['@attributes']['LocationCode'];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivalairportfullname'] = isset($listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']])?$listwithcode[$valueout['ArrivalAirport']['@attributes']['LocationCode']]:$valueout['ArrivalAirport']['@attributes']['LocationCode'];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['arrivaltime'] = $valueout['@attributes']['ArrivalDateTime'];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['flightno'] = $valueout['@attributes']['FlightNumber'];
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['stopornonstop'] = count($inbound)==1?"Non Stop":(count($inbound)-1)." Stop";
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['returnorarrival'] = "inbound";
                    $datatoaddbyamadeus[$keyitinerary]['inoutflightarr'][$counterforinoutflightarray]['layovertimeinsec'] = $layovertimeinseconds;

                    //filter for no of stops
                    $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] = $datatoaddbyamadeus[$keyitinerary]['filternoofstops'] + 1;

                    //layover time in sec for filter  
                    $datatoaddbyamadeus[$keyitinerary]['layovertimeinsec'][] = $layovertimeinseconds;

                    //gathering data for array unique
                    $datatoaddbyamadeus[$keyitinerary]['flightnoarray'][] = $valueout['@attributes']['FlightNumber'];  
                    $datatoaddbyamadeus[$keyitinerary]['marketingairlinearray'][] = $valueout['MarketingAirline']['@attributes']['Code'];  
                    $datatoaddbyamadeus[$keyitinerary]['originarray'][] = $valueout['DepartureAirport']['@attributes']['LocationCode'];
                    $datatoaddbyamadeus[$keyitinerary]['destinationarray'][] = $valueout['ArrivalAirport']['@attributes']['LocationCode'];  
                                      

                     if($forfiltercheck==1)
                     {  
                       //filter for departure date
                       $datatoaddbyamadeus[$keyitinerary]['indeparturedate'] = strtotime($valueout['@attributes']['DepartureDateTime']);
                       $datatoaddbyamadeus[$keyitinerary]['marketingairlinecodein'] = $valueout['MarketingAirline']['@attributes']['Code'];

                     }
                    $forfiltercheck++;
                    $counterforinoutflightarray++;
                    $counterfornoofflights++;
                  }//inbound flights loop 
                  
                   $datatoaddbyamadeus[$keyitinerary]['totalflightcounter'] = $counterfornoofflights-1;
                
          }//for each for bfm results
        

         $mainarrayorresulresults['amadeusresult'] = array_merge($mainarrayorresulresults['amadeusresult'], $datatoaddbyamadeus);        
      }//if bmf $arrayofresbfm if ends here


      //bargain finder max ends
//echo "<pre>"; print_r($mainarrayorresulresults['amadeusresult']); die;        
        //remove duplicate flights
        //$mainarrayorresulresults['amadeusresult']= super_unique($mainarrayorresulresults['amadeusresult']);
        //$mainarrayorresulresults['amadeusresult'] = array_unique($mainarrayorresulresults['amadeusresult']);
//echo "<pre>"; print_r($mainarrayorresulresults['amadeusresult']); die;


      //login to make array unique starts here
      if($mainarrayorresulresults['amadeusresult'])
      {
        $arraytomakeunique = array();
        foreach ($mainarrayorresulresults['amadeusresult'] as $key => $value) 
        {

           if(isset($value["indeparturedate"]) && isset($value["marketingairlinecodein"]) )
           { 
            $arraytomakeunique[$key] = array($value["totalfare"],$value["totaltax"],$value["filternoofstops"],$value["flightnoarray"],$value["marketingairlinearray"],$value["outdeparturedate"],$value["indeparturedate"],$value["marketingairlinecodeout"],$value["marketingairlinecodein"]);
          }
          else
          {
            $arraytomakeunique[$key] = array($value["totalfare"],$value["totaltax"],$value["filternoofstops"],$value["flightnoarray"],$value["marketingairlinearray"],$value["outdeparturedate"],$value["marketingairlinecodeout"]);
          }
            
           

        }

      }
      //make array unique
      //$mainarrayorresulresults['amadeusresult'] = array_unique($mainarrayorresulresults['amadeusresult'], SORT_REGULAR);

      if(isset($arraytomakeunique))
      {
        
        $arrayafterunique = array();

        $arrayafterunique = array_unique($arraytomakeunique, SORT_REGULAR);  

        if($arrayafterunique)
        {
          $arraytoprintafteruniqueaminarray = array();
          foreach ($arrayafterunique as $key => $value) {
            
            
            $arraytoprintafteruniqueaminarray['amadeusresult'][] = $mainarrayorresulresults['amadeusresult'][$key];

          }

          $mainarrayorresulresults['amadeusresult'] = $arraytoprintafteruniqueaminarray['amadeusresult'];

        }

      }
      
      //login to make array unique ends here



     



        if(isset($outboundflightstops) && $outboundflightstops != "")
        {
          
          if($outboundflightstops == 0)
          {
//filter for non stop            
$mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { return $a['filternoofstops'] <= 2;});
          }
          elseif($outboundflightstops == 1)
          {
//filter for 1 stop
$mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { return $a['filternoofstops'] <= 4;});
          }
          elseif($outboundflightstops == 2)
          {

//filter for 2 stop
$mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { return $a['filternoofstops'] <= 6;});
          }
          elseif($outboundflightstops == 3)
          {
//filter for 3 stop
$mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { return $a['filternoofstops'] <= 8;});

          }
          elseif($outboundflightstops == 4)
          {
//filter for 4 stop
$mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { return $a['filternoofstops'] <= 10;});
          }
        
  
        }  
       






      //filter for departure time  
      if(isset($outbounddeparturewindow) && $outbounddeparturewindow != "")
      {
        $str = substr($outbounddeparturewindow,4,4);
        $lowerstr = substr($outbounddeparturewindow,0,4);
        $time = $str;
        $time = substr($time,0,2).':'.substr($time,2,2);

        $timelow = $lowerstr;
        $timelow = substr($timelow,0,2).':'.substr($timelow,2,2);
        
        //console.log(output);
        $filterdatelow = $departuredate ."T". $timelow;

        $filterdatelow = strtotime($filterdatelow);
        $_SESSION['filterdatelow'] = $filterdatelow;

        //console.log(output);
        $filterdate = $departuredate ."T". $time;

        $filterdate = strtotime($filterdate);
        $_SESSION['filterdate'] = $filterdate;
//outdeparturedate
//indeparturedate
$mainarrayorresulresults['filterdate'] =   $filterdate;      
$mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { 
 if(isset($a['indeparturedate'])){ return (( $a['outdeparturedate'] >=$_SESSION['filterdatelow'] && $a['outdeparturedate'] <= $_SESSION['filterdate']) || ($a['indeparturedate']>=$_SESSION['filterdatelow']&&$a['indeparturedate'] <= $_SESSION['filterdate'])); }else{
  return ($a['outdeparturedate']>=$_SESSION['filterdatelow']&&$a['outdeparturedate'] <= $_SESSION['filterdate']);
 }
});
        $_SESSION['filterdate'] = "";
        $_SESSION['filterdatelow']  = "";
      } 
       
        
     
      //filter for flight carrier
      //$includedcarriers 
      if(isset($includedcarriers) && $includedcarriers!="")
      {

        $arrayofcarrier = explode(",", $includedcarriers);
        $_SESSION['arrayofcarrier'] = $arrayofcarrier;
        
        $mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { 
        if(isset($a['marketingairlinecodein'])){ return (in_array($a['marketingairlinecodeout'], $_SESSION['arrayofcarrier']) && in_array($a['marketingairlinecodein'], $_SESSION['arrayofcarrier'])); }else{ return (in_array($a['marketingairlinecodeout'], $_SESSION['arrayofcarrier'])); }
        });

        $_SESSION['arrayofcarrier'] = "";
      }  

      //filter for lay over time
      if(isset($inboundstopduration) && $inboundstopduration!="")
      {

        $_SESSION['layovertimeinseconds'] = $inboundstopduration * 3600;

        //layovertimeinsec
        $mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { 
        return (isset($a['layovertimeinsec']) && $a['layovertimeinsec']!='0m' && (max($a['layovertimeinsec']) <=$_SESSION['layovertimeinseconds']));
        });

        $_SESSION['layovertimeinseconds'] = "";
      }  


        //sort by price
        $price = array();
        foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
        {
            
            $price[$key] = $row['totalfare'];
        }
        array_multisort($price, SORT_ASC, $mainarrayorresulresults['amadeusresult']);

        if(isset($sortingvalue)&&$sortingvalue!= "")
        {
          if($sortingvalue == "high-to-low-price")
          {
            //sort by price
            $price = array();
            foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
            {
                
                $price[$key] = $row['totalfare'];
            }
            array_multisort($price, SORT_DESC, $mainarrayorresulresults['amadeusresult']);

          }
          elseif($sortingvalue == "low-to-high-price")
          {
            //sort by price
            $price = array();
            foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
            {
                
                $price[$key] = $row['totalfare'];
            }
            array_multisort($price, SORT_ASC, $mainarrayorresulresults['amadeusresult']);
            
          }
          elseif($sortingvalue == "waiting-duration-up")
          {
            //sort by waiting
            $waitingtime = array();
            foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
            {
                if(isset($row['layovertimeinsec']))
                {
                  $waitingtime[$key] = max($row['layovertimeinsec']);  
                }
                
                
            }
            array_multisort($waitingtime, SORT_ASC, $mainarrayorresulresults['amadeusresult']);
          }
          elseif($sortingvalue == "waiting-duration-dn")
          {

            //sort by price
            $waitingtime = array();
            foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
            {
                if(isset($row['layovertimeinsec']))
                {
                  $waitingtime[$key] = max($row['layovertimeinsec']);  
                }
               
                
            }
            array_multisort($waitingtime, SORT_DESC, $mainarrayorresulresults['amadeusresult']);
            
          }
          elseif($sortingvalue == "noofstopsasc")
          {

             //sort by price
            $noofstops = array();
            foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
            {
                if(isset($row['filternoofstops']))
                {
                  $noofstops[$key] = $row['filternoofstops'];  
                }
               
                
            }
            array_multisort($noofstops, SORT_ASC, $mainarrayorresulresults['amadeusresult']);
            
          }
          elseif($sortingvalue == "noofstopsdecs")
          {
              //sort by price
            $noofstops = array();
            foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
            {
                if(isset($row['filternoofstops']))
                {
                  $noofstops[$key] = $row['filternoofstops'];  
                }
               
                
            }
            array_multisort($noofstops, SORT_DESC, $mainarrayorresulresults['amadeusresult']);
          }
        }




        echo json_encode($mainarrayorresulresults);

        //$new = array_filter($old,function($a) {return $a >= 6;});
die;
/////////////////////////////////////////////////////////////////////////////////////////////////////////

 $price = array();
        foreach ($mainarrayorresulresults['amadeusresult'] as $key => $row)
        {
            
            $price[$key] = $row['totalfare'];
        }
        array_multisort($price, SORT_ASC, $mainarrayorresulresults['amadeusresult']);
        //echo json_encode($mainarrayorresulresults);

        $mainarrayorresulresults['amadeusresult'] = array_filter($mainarrayorresulresults['amadeusresult'],function($a) { return $a['filternoofstops'] <= 222;});

        echo json_encode($mainarrayorresulresults);
die;

      //Bargain BargainFinderMax start
      //include_once 'workflow/Workflow.php';
      include_once 'soap_activities/BargainFinderMaxSoapActivity.php';


      $workflow = new Workflow(new BargainFinderMaxSoapActivity());
      $resultbfm = $workflow->runWorkflow();

      $arrayofresbfm = (array) $resultbfm;
      if($arrayofresbfm)
      {

       
        $bypassfirstelementofarraybfm = reset($arrayofresbfm);
         

        $requestofxmlbfm = $bypassfirstelementofarraybfm['BargainFinderMaxRQ'];
        
        $arraybfmrequestarray = XML2Array::createArray($requestofxmlbfm);

        $responseofbfm = $bypassfirstelementofarraybfm['BargainFinderMaxRS'];

        $arraybfmresponsearray = XML2Array::createArray($responseofbfm);
        
        echo "<pre>";
        print_r($arraybfmresponsearray);
        die;
        print_r($bypassfirstelementofarraybfm); 
        echo "xml printing here";

        die;
      }
      
      //Bargain BargainFinderMax ends

//print_r($result{'results:SharedContext:private'}['LeadPriceCalendar']);
//print_r($result['results:SharedContext:private']['InstaFlight']);
//print_r($result['results:SharedContext:private']['BargainFinderMax']);


die;
/*ob_start();
var_dump($result);
$dump = ob_get_clean();
echo $dump;
flush();*/


function super_unique($array)
{
  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

  foreach ($result as $key => $value)
  {
    if ( is_array($value) )
    {
      $result[$key] = super_unique($value);
    }
  }

  return $result;
}


function getamadeus($url, $params=array()) 
{	

    $url = $url.'?'.http_build_query($params, '', '&');
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
    $response = curl_exec($ch);
    
    curl_close($ch);
    
    return $response;
}


function secondsToTime($seconds) {

  // extract hours
  $hours = floor($seconds / (60 * 60));

  // extract minutes
  $divisor_for_minutes = $seconds % (60 * 60);
  $minutes = floor($divisor_for_minutes / 60);

  // extract the remaining seconds
  $divisor_for_seconds = $divisor_for_minutes % 60;
  $seconds = ceil($divisor_for_seconds);

  // return the final array
  $obj = array(
      "h" => (int) $hours,
      "m" => (int) $minutes,
      "s" => (int) $seconds,
   );

  return $obj;
}

/**
 * XML2Array: A class to convert XML to array in PHP
 * It returns the array which can be converted back to XML using the Array2XML script
 * It takes an XML string or a DOMDocument object as an input.
 *
 * See Array2XML: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 * Version: 0.1 (07 Dec 2011)
 * Version: 0.2 (04 Mar 2012)
 *      Fixed typo 'DomDocument' to 'DOMDocument'
 *
 * Usage:
 *       $array = XML2Array::createArray($xml);
 */

class XML2Array {

    private static $xml = null;
  private static $encoding = 'UTF-8';

    /**
     * Initialize the root XML node [optional]
     * @param $version
     * @param $encoding
     * @param $format_output
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $format_output = true) {
        self::$xml = new DOMDocument($version, $encoding);
        self::$xml->formatOutput = $format_output;
    self::$encoding = $encoding;
    }

    /**
     * Convert an XML to Array
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return DOMDocument
     */
    public static function &createArray($input_xml) {
        $xml = self::getXMLRoot();
    if(is_string($input_xml)) {
      $parsed = $xml->loadXML($input_xml);
      if(!$parsed) {
        throw new Exception('[XML2Array] Error parsing the XML string.');
      }
    } else {
      if(get_class($input_xml) != 'DOMDocument') {
        throw new Exception('[XML2Array] The input XML object should be of type: DOMDocument.');
      }
      $xml = self::$xml = $input_xml;
    }
    $array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $array;
    }

    /**
     * Convert an Array to XML
     * @param mixed $node - XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node) {
    $output = array();

    switch ($node->nodeType) {
      case XML_CDATA_SECTION_NODE:
        $output['@cdata'] = trim($node->textContent);
        break;

      case XML_TEXT_NODE:
        $output = trim($node->textContent);
        break;

      case XML_ELEMENT_NODE:

        // for each child node, call the covert function recursively
        for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
          $child = $node->childNodes->item($i);
          $v = self::convert($child);
          if(isset($child->tagName)) {
            $t = $child->tagName;

            // assume more nodes of same kind are coming
            if(!isset($output[$t])) {
              $output[$t] = array();
            }
            $output[$t][] = $v;
          } else {
            //check if it is not an empty text node
            if($v !== '') {
              $output = $v;
            }
          }
        }

        if(is_array($output)) {
          // if only one node of its kind, assign it directly instead if array($value);
          foreach ($output as $t => $v) {
            if(is_array($v) && count($v)==1) {
              $output[$t] = $v[0];
            }
          }
          if(empty($output)) {
            //for empty nodes
            $output = '';
          }
        }

        // loop through the attributes and collect them
        if($node->attributes->length) {
          $a = array();
          foreach($node->attributes as $attrName => $attrNode) {
            $a[$attrName] = (string) $attrNode->value;
          }
          // if its an leaf node, store the value in @value instead of directly storing it.
          if(!is_array($output)) {
            $output = array('@value' => $output);
          }
          $output['@attributes'] = $a;
        }
        break;
    }
    return $output;
    }

    /*
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot(){
        if(empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }
}


