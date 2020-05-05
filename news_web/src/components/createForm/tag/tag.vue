
<template>
  <div class="createTag">
    <el-form :model="result" :rules="rules">
      
      <el-form-item prop="level">
        <el-select v-model="result.level" placeholder="请选择标签级别">
          <el-option v-for="item in levelOptions" :key="item.value" :label="item.label" :value="item.value"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item prop="name"><el-input v-model="result.name" placeholder="请输入标签名"></el-input></el-form-item>

      <el-form-item class="button"><!-- 提交按钮 -->
        <el-button @click="submit">提 交</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>

export default {

  data() {
    return {
      result: {
        name: '',
        level: null,
      },

      levelOptions: [
        { value: 1, label: "一级" },
        { value: 2, label: "二级" },
        { value: 3, label: "三级" },
        { value: 4, label: "指定推荐" },
      ],
        
      rules: {
        name: [
          { required: true, message: '标签名不能为空', trigger: 'blur' },
          { required: true, message: '标签级别不能为空', trigger: 'blur' },
        ],
      }
    }
  },

  computed: {

  },

  methods: {
    // 提交
    async submit() {
      async function callback() {
        let { data: { code, message } } = await this.$gAxios().post('tag/add', { name, level });
        if(code === 200)
          this.$notify.success({ title: message, message: '标签已加入数据库' });
        else
          this.$notify.error({ title: message, message: '添加失败' });
      }
      let { result: { name, level } } = this;
      if(!name || !level) { // 进行参数校验，name、level 不能为空
        this.$notify.warning({ title: '警告', message: '标签名、标签级别都不能为空' });
        return;
      }
      let confirmParams = { confirmButtonText: '确定', cancelButtonText: '取消', type: 'info' };
      this.$confirm('确认提交吗?', '提示', confirmParams).then(callback.bind(this)).catch(() => null); // 提交前先弹出提示确认
    },
  },

  mounted() {

  }
};

</script>

<style lang="scss">
  @import "tag.scss";
</style>
