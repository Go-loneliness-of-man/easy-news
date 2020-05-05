
<template>
  <div class="createTheme">
    <el-form class="themeForm" :model="result" :rules="rules"><!-- 表单 -->

      <el-form-item class="title" prop="title"><!-- 专题标题 -->
        <el-input v-model="result.title" placeholder="请输入专题名称"></el-input>
      </el-form-item>

      <el-form-item class="master" prop="master"><!-- 主标签，单选 -->
        <el-select v-model="result.master" filterable remote reserve-keyword placeholder="请输入关键字查询并添加主标签，必填" :remote-method="getMasterOption" :loading="masterLoading">
          <el-option v-for="item in masterOption" :key="item.id" :label="item.label" :value="item.id"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item class="branch" prop="branch"><!-- 副标签，多选 -->
        <el-select v-model="result.branch" multiple filterable remote reserve-keyword placeholder="请输入关键字查询并添加副标签，可选" :remote-method="getBranchOption" :loading="branchLoading">
          <el-option v-for="item in branchOption" :key="item.id" :label="item.label" :value="item.id"></el-option>
        </el-select>
      </el-form-item>

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
      result: { title: '', master: null, branch: null, }, // 结果对象

      masterOption: [],
      masterLoading: false,

      branchOption: [],
      branchLoading: false,

      rules: {
        title: [ { required: true, message: '该项不能为空', trigger: 'blur' } ],
        master: [ { required: true, message: '该项不能为空', trigger: 'blur' } ],
      },
    }
  },

  computed: {

  },

  methods: {

    // 提交
    async submit() {
      async function callback() {
        branch = JSON.stringify(branch);
        let { data: { code, message } } = await this.$gAxios().post('theme/add', { title, master, branch });
        if(code === 200)
          this.$notify.success({ title: message, message: '专题已加入数据库' });
        else
          this.$notify.error({ title: message, message: '添加失败' });
      }
      let { result: { title, master, branch } } = this;
      if(!title || !master) { // 进行参数校验，title、master 不能为空
        this.$notify.warning({ title: '警告', message: '专题标题、主标签都不能为空' });
        return;
      }
      let confirmParams = { confirmButtonText: '确定', cancelButtonText: '取消', type: 'info' };
      this.$confirm('确认已编辑完毕并提交吗?', '提示', confirmParams).then(callback.bind(this)).catch(() => null); // 提交前先弹出提示确认
    },

    async getMasterOption(string) {
      if (string !== '') {
        this.masterLoading = true;
        // this.masterOption = await this.$gAxios().post('news/add', { content });
        this.masterOption = [ { id: 1, label: 'asdasd' } ];
        this.masterLoading = false;
      } else
        this.masterOption = [];
    },

    async getBranchOption(string) {
      if (string !== '') {
        this.branchLoading = true;
        // this.branchOption = await this.$gAxios().post('news/add', { content });
        this.branchOption = [ { id: 1, label: 'asdasd' }, { id: 2, label: 'aasdsdasd' }, { id: 3, label: 'aasdsdassd' } ];
        this.branchLoading = false;
      } else
        this.branchOption = [];
    },
  },

  mounted() {

  }
};

</script>

<style lang="scss">
  @import "theme.scss";
</style>
