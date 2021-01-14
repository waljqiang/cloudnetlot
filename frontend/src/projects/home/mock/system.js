import Mock from 'mockjs'

export default {
     countrycode:() => {
          return {
               "status": 10000,
               "data": {
                    "total": 1,
                    "list": [
                         {
                           "name": "安道尔",
                           "short2": "AD",
                           "short3": "AND",
                           "num": "020",
                           "phonecode": "376"
                         }
                   ]
               },
               "errorCode": [600000102111]
           }
     },
     
}
