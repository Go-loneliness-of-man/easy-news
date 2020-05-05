
/*
  rw：包含 rw 的所有对象，并去掉它们的前缀 rw
*/



/*
  rwPublic：常用公共方法

  目前已有的方法：

  1、type(data)，返回变量的类型，精确到类名

  2、deepCopy(x)，递归深拷贝

  3、rand(low, high, don = 0)，生成指定区间随机数，参数依次是下限、上限、小数位长度
*/

(function(window){

  // 声明类
  class rwPublic {

    //构造函数
    constructor() {

    }

    // 判断变量类型
    type(data) {
      const type = Object.prototype.toString.call(data).toLowerCase();
      return type.replace(/^\[object\s(\w+)\]$/, (...rest) => {
        return rest[1];
      });
    }

    // 递归深拷贝
    deepCopy(x) {
      if (this.type(x) !== 'object') return '必须传入对象';      // 若不是对象则结束
      const target = Array.isArray(x) ? [] : {};                // 判别是数组还是对象
      for (const k in x)                                        // 循环拷贝
        if (x.hasOwnProperty(k))                                // 判断属性是否在对象自身上
          if (this.type(x[k]) === 'object')                     // 若是对象，递归
            target[k] = this.deepCopy(x[k]);
          else
            target[k] = x[k];
      return target;
    }

    // 生成指定区间随机整数
    rand(low, high, don = 0) {
      let num;
      for (num = 1; don; num *= 10, don--);
      return num > 1 ? parseInt((low + Math.random() * (high - low)) * num) / num : parseInt(low + Math.random() * (high - low));
    }
  }

  window.rwPublic = new rwPublic();                                  // 将类挂在 window 上
})(window);



/*
  rwArray：数组相关

  目前已有的方法：

  1、notRepeat(arr, target, compare = undefined, index = 0)，数组查重，参数：
    arr：          array               被查询的数组
    target：       any                 目标元素
    compare：      function            比较器，默认直接比较 arr[i]、target 是否相等，传了后会将 arr[i]、target 依次传入比较器进行比较，返回 1 代表不重复，0 代表重复
    index：        int                 是否返回重复项的下标，默认 0，若传 1，返回值变为对象 { repeat: int, i: int }，repeat 即原返回值，i 是下标

  2、convertTree(data = [{ id: '1' }])，数组转 tree，每个对象以 id 为标识，例如 1、12、13、14，则 12、13、14 是 1 的子节点
*/

(function(window) {

  // 声明类
  class rwArray {

    //构造函数
    constructor() {

    }

    // 数组查重，参数依次是数组、目标元素、比较器（回调函数）、是否重复项的返回下标（此时返回对象 { repeat: int, i: int }），重复返回 0，不重返回 1
    notRepeat(arr, target, compare = undefined, index = 0) {
      let i = 0;
      if (compare)                                          // 判断是否存在比较器
        for (i = 0; i < arr.length; i++)                    // 遍历比较
          if (compare(arr[i], target))                      // 调用比较器
            return index ? { repeat: 0, i } : 0;            // 判断是否返回下标
      else
        for (i = 0; i < arr.length; i++)
          if (arr[i] === target)
            return index ? { repeat: 0, i } : 0;            // 判断是否返回下标
      return index ? { repeat: 1, i } : 1;                  // 判断是否返回下标
    }

    // 思路：先遍历获取 id 的长度列表，然后获取一级节点，再循环获取每个一级节点的子节点，而对于每个节点，若其仍有子节点则继续向下递归，直到不存在子节点便返回
    // 参数是待转换数组
    convertTree(data = [{ id: '1' }]) { // 数组转换 tree
      function parse(data, father, lenList = [], lenIndex = 0) { // 递归生成子节点
        let match = data.filter(v => v.id.length === lenList[lenIndex + 1] && father.id === v.id.slice(0, lenList[lenIndex])); // 匹配长度、前缀
        if(match.length) // 当存在子节点时，继续搜索生成子节点
          father.children = match.map(v => parse(data, v, lenList, lenIndex + 1));
        return father;
      }

      let lenList = [...new Set(data.map(v => v.id.length))]; // 获取 id 长度列表
      let father = data.filter(v => v.id.length === lenList[0]).map(v => ({ id: v.id, children: [] })); // 获取一级节点
      father.forEach(v => parse(data, v, lenList)); // 遍历一级节点
      return father;
    }
  }

  window.rwArray = new rwArray();                           // 将类挂在 window 上
})(window);




/*
  reDate：日期相关

  目前已有的方法：

  1、localTime(format = 'y-mon-d ', num = (new Date()).getTime())，获取指定格式的日期，参数：
    format：      string                           格式字符串，默认为 'y-mon-d'，可选字符有 'y', 'mon', 'd', 'h', 'm', 's'
    num：         int                              时间戳，默认当前时间

  2、addOneDate(date, format = 0)，加一天，date 为接收的日期，格式必须为 y-mon-d 或 y-mon-d h:m:s，format 为返回日期的格式

  3、cutOneDate(date, format = 0)，同 addOneDate

  4、compareDate(a, b)，日期比较，a、b 为接收的日期，格式必须为 y-mon-d 或 y-mon-d h:m:s，a 大返回 1，b 大返回 0，相等返回 2

  5、supplementaryDate(data, key, start, end)，日期补全，补全的元素拥有 supplementaryDate 属性，值为 true，参数：
    data：        [object, object, object ...]      待补全日期的数组，并且数组日期必须由小到大
    key：         string                            数组元素中，保存日期的 key
    start：       string                            起始日期
    end：         string                            结束日期
  
  6、timeOverlap(times, y = 0)，判断时间区间是否存在重叠，返回值 0 代表重叠，1 代表不重叠，参数：
    times:        [[string, string], ... ]          被判断的时间区间字符串，左端点一定要小于右端点
    y             int                               时间字符串格式，默认 0，格式为 时:分:秒，代表仅判断 24 小时，不考虑年月日，传入 1 则可以指定年、月、日
*/

(function(window){

  // 声明类
  class rwDate {

    //构造函数
    constructor() {

    }

    // 获取指定格式的日期，第一个参数为格式字符串，第二个参数（可选）为时间戳，不传默认采用当前时间
    localTime(format = 'y-mon-d ', num = (new Date()).getTime()) {
      let t = [];
      let tag = [ 'y', 'mon', 'd', 'h', 'm', 's' ];
      [ t[0], t[1], t[2] ] = (new Date(num)).toLocaleDateString().split(/[$\/\\-]/i);   // 获取年、月、日
      [ t[3], t[4], t[5] ] = (new Date(num)).toString().split(' ')[4].split(':');       // 获取时、分、秒
      for (let i = 0; i < 3; i++)                                             // 补全十位的 0
        t[i] = t[i] < 10 ? `0${t[i]}` : t[i];
      for (let i = 0; i < tag.length; i++)                                    // 替换为指定格式
        format = format.replace(tag[i], t[i]);
      return format;
    }

    // 加一天
    addOneDate(date, format = 0) {
      let num = (new Date(date)).getTime();                                   // 日期字符串转时间戳
      return this.localTime(format ? format : 'y-mon-d', num + 24 * 3600000); // 转为指定的格式返回
    }

    // 减一天
    cutOneDate(date, format = 0) {
      let num = (new Date(date)).getTime();                                   // 日期字符串转时间戳
      return this.localTime(format ? format : 'y-mon-d', num - 24 * 3600000); // 转为指定格式返回
    }

    // 比较两个日期的大小，a 大返回 1，b 大返回 0，相等返回 2
    compareDate(a, b) {
      a = (new Date(a)).getTime();
      b = (new Date(b)).getTime();
      return a === b ? 2 : a > b;
    }

    // 生成一段任意格式的连续日期
    async date(start, end, format = 0) {
      let time = [];
      for (let date = start; date !== end; date = this.addOneDate(date))      // 循环生成
        time[time.length] = this.localTime(format ? format : 'y-mon-d', (new Date(date)).getTime());    // 转换格式
      time[time.length] = this.localTime(format ? format : 'y-mon-d', (new Date(end)).getTime());       // 添加结束日期
      return time;                                                            // 返回数组
    }

    // 日期补全，参数 data、key、start、end 依次是数组、数组元素日期的 key、开始日期、结束日期
    // 遍历原数组，每次取出元素的 year、mon、day，判断是否与新数组最后一个元素的日期连续，若不连续则开始补全，直到与下个元素的日期连续，最终返回补全的数组，补全的元素上会有个额外的 supplementaryDate 属性，并且值为 true
    supplementaryDate(data, key, start, end) {
      if (window.rwPublic.type(data) !== 'array')                        // 参数校验
        return '第一个参数必须是数组';
      const newData = [];

      // 检测原数组是否为空
      if (data.length) {
        if (window.rwPublic.type(data) !== 'array') return '数组元素必须是对象';      // 参数校验
        if (data[0][key].split(/[$\/\\-]/i).length !== 3) { return '日期必须是 xxxx-xx-xx 格式'; }

        // 准备新数组
        if (start !== data[0][key]) {                                          // 判断是否需要补全第一个日期
          newData[0] = {};                                                     // 创建第一个元素
          newData[0][key] = start;                                             // 日期起始点
          newData[0].supplementaryDate = true;                                 // 添加补全标记
        }
        else newData[0] = window.rwPublic.deepCopy(data[0]);

        // 遍历原数组
        for (let i = start === data[0][key] ? 1 : 0; i < data.length && newData[newData.length - 1][key] !== data[i][key];) {
          if (this.addOneDate(newData[newData.length - 1][key]) === data[i][key])                       // 判断日期是否连续
            newData.push(window.rwPublic.deepCopy(data[i++]));                                          // 连续
          else                                                                 // 不连续，开始补全
            for (; !this.compareDate(this.addOneDate(newData[newData.length - 1][key]), data[i][key]);) {
              newData[newData.length] = {};                                    // 创建元素
              newData[newData.length - 1][key] = this.addOneDate(newData[newData.length - 2][key]);     // 补全日期
              newData[newData.length - 1].supplementaryDate = true;            // 添加补全标记
            }
        }

        // 补全 end 前的所有日期
        for (; !this.compareDate(newData[newData.length - 1][key], end);) {    // 遍历剩余部分
          newData[newData.length] = {};                                        // 创建元素
          newData[newData.length - 1][key] = this.addOneDate(newData[newData.length - 2][key]);// 补全日期
          newData[newData.length - 1].supplementaryDate = true;                // 添加补全标记
        }
      }
      else {                                                                   // data 为 空数组，直接补全全部日期
        newData[0] = {};                                                       // 创建第一个元素
        newData[0][key] = start;                                               // 日期起始点
        newData[0].supplementaryDate = true;                                   // 添加补全标记
        for (; !this.compareDate(newData[newData.length - 1][key], end);) {
          newData[newData.length] = {};                                        // 创建元素
          newData[newData.length - 1][key] = this.addOneDate(newData[newData.length - 2][key]);         // 补全日期
          newData[newData.length - 1].supplementaryDate = true;                // 添加补全标记
        }
      }
      return newData;
    }

    // 判断时间区间是否存在重叠，返回值 0 代表重叠，1 代表不重叠
    async timeOverlap(times, y = 0) {
      for (let i = 0; i < times.length; i++) // 全部转为时间戳
        for(let j = 0; j < 2; j++)
          times[i][j] = (new Date(y ? times[i][j] : `2019-2-22 ${times[i][j]}`)).getTime();
      for (let i = 0; i < times.length; i++) // 遍历元素
        for (let j = i + 1; j < times.length; j++) // 单独拿出一个区间进行遍历，假设 A、B 区间，A、B 区间端点 a、b、c、d，则只要 a <= c，b <= c 或 a >= d，A、B 不重叠
          if (times[i][0] <= times[j][0] && times[i][1] <= times[j][0]) // a < c、b < c
              continue;
          else if (times[i][0] >= times[j][1]) // a > d
            continue;
          else
            return 0;
      return 1;
    }
  }

  window.rwDate = new rwDate();                                                 // 将类挂在 window 上
})(window);


(function(window) {

  // 声明类
  class rw {

    //构造函数
    constructor() {
      this.public = window.rwPublic;
      this.array = window.rwArray;
      this.date = window.rwDate;
    }
  }

  window.rw = new rw();                                                         // 将类挂在 window 上
})(window);

